import React, {useState, useEffect} from 'react';
import Card from "../../../components/Card/Card";
import CardHeader from "../../../components/Card/CardHeader";
import CardBody from "../../../components/Card/CardBody";
import Table from '@material-ui/core/Table';
import TableBody from '@material-ui/core/TableBody';
import TableCell from '@material-ui/core/TableCell';
import TableHead from '@material-ui/core/TableHead';
import TableRow from '@material-ui/core/TableRow';
import {makeStyles} from "@material-ui/core/styles";
import {generatePath, Link} from "react-router-dom";
import moment from 'moment';
import MonthInput from "../../../components/CustomInput/MonthInput";
import dataProvider from "../../../dataProvider";


const styles = {
    cardCategoryWhite: {
        color: "rgba(255,255,255,.62)",
        margin: "0",
        fontSize: "14px",
        marginTop: "0",
        marginBottom: "0",
    },
    cardTitleWhite: {
        color: "#FFFFFF",
        marginTop: "0px",
        minHeight: "auto",
        fontWeight: "300",
        fontFamily: "'Roboto', 'Helvetica', 'Arial', sans-serif",
        marginBottom: "3px",
        textDecoration: "none",
    },
};
const useStyles = makeStyles(styles);

const GroupDialogs = props => {
    const classes = useStyles();
    const [data, setData] = useState([]);
    const [date, setDate] = useState({
        startOfMonth: moment().startOf('month').format('YYYY-MM-DD HH:mm:ss'),
        endOfMonth: moment().endOf('month').format('YYYY-MM-DD HH:mm:ss')
    });
    const fetchData = async () => {
        await dataProvider({
            params: {
                'dialogs.date[after]': date.startOfMonth,
                'dialogs.date[before]': date.endOfMonth
            }
        }).get('/users/scoring').then(res => {
            let trainee = res.data.filter((a) => a?.trainee).sort((a, b) => b?.totalScore - a?.totalScore);
            let operators = res.data.filter((a) => !a?.trainee).sort((a, b) => b?.totalScore - a?.totalScore);
            let scoring = [...operators, ...trainee];
            setData(scoring);
        })
    }
    useEffect(async () => {
        fetchData();
    }, [date]);
    let operators_ids = props?.user?.operators?.map(user_uri => user_uri.split('/').pop());
    let operators = data.filter(u => {
        return operators_ids?.includes(u.id);
    }).sort((a, b) => b?.scoring - a?.scoring).sort((a, b) => a?.trainee - b?.trainee);
    return <>
        <h1>Диалоги</h1>
        <Card>
            <CardHeader color="primary">
                <h4 className={classes.cardTitleWhite}>
                    Оценки операторов за <MonthInput setDate={setDate}/>
                </h4>
            </CardHeader>
            <CardBody>
                <Table className={classes.table} aria-label="simple table">
                    <TableHead>
                        <TableRow>
                            <TableCell>ФИ оператора</TableCell>
                            <TableCell align={"right"}>Коммуникаций</TableCell>
                            <TableCell align={"right"}>Контроль качества</TableCell>
                            <TableCell align={"right"}>Оцениваний</TableCell>
                            <TableCell align={"right"}>Критические</TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        {operators.map((row, index) => {

                            return <TableRow key={index} style={
                                row.trainee
                                    ? {backgroundColor: "#7e7e7e", boxShadow: `inset 0px 0px 5px 5px ${row.zone?.color}`}
                                    : {backgroundColor: row.zone?.color}
                            }>
                                <TableCell component="th" scope="row">
                                    <Link to={generatePath('/user/:userId/dialogs?date[startOfMonth]=:startOfMonth&date[endOfMonth]=:endOfMonth', {
                                        userId: row.id,
                                        "startOfMonth": date.startOfMonth,
                                        "endOfMonth": date.endOfMonth,
                                    })} style={{
                                        color: '#111111',
                                        textDecoration: 'none',
                                        textShadow: '0px 0px 1px black'
                                    }}
                                    >{row.name}</Link>
                                </TableCell>
                                <TableCell align={"right"}>{row.dialogs}</TableCell>
                                <TableCell align={"right"}>{row.scoring}</TableCell>
                                <TableCell align={"right"}>{row.scoringCount}</TableCell>
                                <TableCell align={"right"}>{row.criticalErrors}</TableCell>
                            </TableRow>;
                        })}
                    </TableBody>
                </Table>
            </CardBody>
        </Card>
    </>;
}

export default GroupDialogs;