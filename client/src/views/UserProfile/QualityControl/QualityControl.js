import React, {useEffect, useState} from "react";
import Card from "../../../components/Card/Card";
import CardHeader from "../../../components/Card/CardHeader";
import CardBody from "../../../components/Card/CardBody";
import Paper from '@material-ui/core/Paper';
import Grid from '@material-ui/core/Grid';
import {makeStyles} from "@material-ui/core/styles";
import dataProvider from "../../../dataProvider";
import moment from 'moment';
import Table from '@material-ui/core/Table';
import TableBody from '@material-ui/core/TableBody';
import TableHead from '@material-ui/core/TableHead';
import TableRow from '@material-ui/core/TableRow';
import TableCell from "@material-ui/core/TableCell";

import HeadsetMicIcon from '@material-ui/icons/HeadsetMic';
import ChatIcon from '@material-ui/icons/Chat';

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

const QualityControl = (props) => {
    const classes = useStyles();
    const [selectedDialog, setSelectedDialog] = useState();
    const [dialogs, setDialogs] = useState([]);
    const fetchDialogs = async () => {
        dataProvider({
            params: {
                'date[after]': moment().startOf('month').format('YYYY-MM-DD HH:mm:ss'),
                'date[before]': moment().endOf('month').format('YYYY-MM-DD HH:mm:ss'),
                'exists[scoring]': true,
                'user': props.user.id,
            }
        }).get('/dialogs').then(res => {setDialogs(res.data);})
    }
    useEffect(async () => {
        await fetchDialogs();
    }, [])
    const handleClick = data => {
        setDialogs(data);
    }

    return <>
        <h1>Контроль качества</h1>
        <Card>
            <CardHeader color="primary">
                <h4 className={classes.cardTitleWhite}>За текущий месяц</h4>
            </CardHeader>
            <CardBody>
                {dialogs &&
                    <Table>
                        <TableHead>
                            <TableRow>
                                <TableCell>
                                    ID
                                </TableCell>
                                <TableCell>
                                    Тип
                                </TableCell>
                                <TableCell>
                                    продолжительность
                                </TableCell>
                                <TableCell>
                                    количество записей
                                </TableCell>
                                <TableCell>
                                    время первого ответа
                                </TableCell>
                                <TableCell>
                                    среднее время ответа
                                </TableCell>
                                <TableCell>
                                    Scoring
                                </TableCell>
                            </TableRow>
                        </TableHead>
                        <TableBody>
                            {dialogs.map(dialog => {
                                return (
                                    <TableRow item xs={12} style={{margin: '15px auto'}}>
                                        <TableCell>{dialog.chatId}</TableCell>
                                        <TableCell>{dialog.type}</TableCell>
                                        <TableCell>{dialog.length}</TableCell>
                                        <TableCell>{dialog.records.length}</TableCell>
                                        <TableCell>{dialog.secondsToFirstAnswer}</TableCell>
                                        <TableCell>{dialog.averageSpeedAnswer}</TableCell>
                                        <TableCell>{dialog.score}</TableCell>
                                    </TableRow>
                                )
                            })}
                        </TableBody>
                    </Table>
                }
            </CardBody>
        </Card>

    </>;
}

export default QualityControl;

