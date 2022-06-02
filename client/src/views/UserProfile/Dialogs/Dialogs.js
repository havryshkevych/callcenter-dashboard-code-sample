import React, {useState} from "react";
import Card from "../../../components/Card/Card";
import CardHeader from "../../../components/Card/CardHeader";
import CardBody from "../../../components/Card/CardBody";
import Table from '@material-ui/core/Table';
import TableBody from '@material-ui/core/TableBody';
import TableCell from '@material-ui/core/TableCell';
import TableHead from '@material-ui/core/TableHead';
import TableRow from '@material-ui/core/TableRow';
import TableFooter from '@material-ui/core/TableFooter';
import TablePagination from '@material-ui/core/TablePagination';
import {makeStyles} from "@material-ui/core/styles";
import HeadsetMicIcon from '@material-ui/icons/HeadsetMic';
import ChatIcon from '@material-ui/icons/Chat';
import MenuItem from '@material-ui/core/MenuItem';
import Select from '@material-ui/core/Select';
import {useLocation} from 'react-router-dom';

import dataProvider from "../../../dataProvider";
import moment from 'moment';
import ShowDialogButton from "../../../components/CustomButtons/ShowDialogButton";
import MonthInput from "../../../components/CustomInput/MonthInput";

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

const useQuery = function() {
    return new URLSearchParams(useLocation().search);
}

const Dialogs = (props) => {
    let query = useQuery();
    const classes = useStyles();
    const [date, setDate] = useState({
        startOfMonth: typeof query.get('date[startOfMonth]') === 'string' ? query.get('date[startOfMonth]') : moment().startOf('month').format('YYYY-MM-DD HH:mm:ss'),
        endOfMonth: typeof query.get('date[endOfMonth]') === 'string' ? query.get('date[endOfMonth]') : moment().endOf('month').format('YYYY-MM-DD HH:mm:ss')
    });
    const [dialogs, setDialogs] = React.useState([]);
    const [scoringExists, setScoringExists] = React.useState(-1)
    const [page, setPage] = React.useState(0);
    const [pageCount, setPageCount] = React.useState(0);
    const [rowsPerPage, setRowsPerPage] = React.useState(10);
    const handleChangeRowsPerPage = (event) => {
        setRowsPerPage(parseInt(event.target.value, 10));
        setPage(1);
    };

    React.useEffect(() => {
        let active = true;
        let paramsData = {
            users: props.user.id,
            page: page + 1,
            itemsPerPage: rowsPerPage,
            "date[after]": date.startOfMonth,
            "date[before]": date.endOfMonth
        };

        if (scoringExists !== -1) {
            paramsData["exists[scoring]"] = scoringExists;
        }

        dataProvider({
            headers: {
                'Accept': 'application/ld+json',
                'Content-Type': 'application/ld+json'
            }
        }).get('/dialogs', {
            params: paramsData
        }).then(res => {
            if (active) {
                setPageCount(res.data["hydra:totalItems"]);
                setDialogs(res.data["hydra:member"]);
            }
        })
        return () => {
            return active = false;
        };
    }, [props.user, scoringExists, rowsPerPage, page, date]);
    const currentScoring = ({scoring}) => {
        if (Array.isArray(scoring)) {
            return scoring.filter(score => score.userId === props.user.id)[0]
        }
        return undefined;
    };
    const showScore = (record) => {
        let userScore = currentScoring(record);

        if (userScore === undefined) {
            return '-';
        }

        let hasCritical = userScore.evaluations.filter(evaluation => evaluation?.criteria?.critical && evaluation?.value);
        if (hasCritical.length) {
            return <span style={{color:'#a10000'}}>обнуленно</span>;
        }

        return userScore?.score;
    }

    return <>
        <h1>Диалоги</h1>
        <Card>
            <CardHeader color="primary">
                <Select
                    value={scoringExists}
                    onChange={event => setScoringExists(event.target.value)}
                    style={{color: "#eee"}}
                >
                    <MenuItem value={-1}><em>Все диалоги</em></MenuItem>
                    <MenuItem value={1}>С оценкой</MenuItem>
                    <MenuItem value={0}>Без оценки</MenuItem>
                </Select>
                <span style={{fontSize: '1.2em', fontFamily: 'monospace'}}> за </span>
                <MonthInput style={{color:"#FFF"}} setDate={setDate} initialValue={date.startOfMonth}/>
            </CardHeader>
            <CardBody>
                <Table className={classes.table} aria-label="simple table">
                    <TableHead>
                        <TableRow>
                            <TableCell>Тип</TableCell>
                            <TableCell>Дата диалога</TableCell>
                            <TableCell>Оценка</TableCell>
                            <TableCell>#</TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        {dialogs.map((row, index) => (
                            <TableRow key={index}>
                                <TableCell component="th" scope="row">
                                    {row.type === "chat" && <ChatIcon/>}
                                    {row.type === "call" && <HeadsetMicIcon/>}
                                </TableCell>
                                <TableCell>{moment(row.date, '').format('DD-MM-YY HH:mm')}</TableCell>
                                <TableCell>{showScore(row)}</TableCell>
                                <TableCell>
                                    <ShowDialogButton record={row} userScoring={currentScoring(row)} user={props.user}/>
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                    <TableFooter>
                        <TableRow>
                            <TablePagination
                                rowsPerPageOptions={[10, 25, 50]}
                                colSpan={3}
                                count={pageCount}
                                rowsPerPage={rowsPerPage}
                                page={page}
                                onChangePage={(event, newPage) => {setPage(newPage)} }
                                onChangeRowsPerPage={handleChangeRowsPerPage}
                            />
                        </TableRow>
                    </TableFooter>
                </Table>
            </CardBody>
        </Card>

    </>;
}

export default Dialogs;

