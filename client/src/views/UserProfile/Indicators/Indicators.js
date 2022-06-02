import React from "react";
import Card from "../../../components/Card/Card";
import CardHeader from "../../../components/Card/CardHeader";
import CardBody from "../../../components/Card/CardBody";
import Table from "../../../components/Table/Table";
import TableRow from "@material-ui/core/TableRow";
import TableCell from "@material-ui/core/TableCell";
import HeadsetMicIcon from '@material-ui/icons/HeadsetMic';
import ChatIcon from '@material-ui/icons/Chat';
import FunctionsIcon from '@material-ui/icons/Functions';
import PieChartIcon from '@material-ui/icons/PieChart';
import {makeStyles} from "@material-ui/core/styles";
import moment from 'moment';
import Typography from '@material-ui/core/Typography';
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

function Indicator({title, subTitle, style, ...props}) {
    return <div style={{textAlign: "center", ...style}} {...props}>
        <b>{title}</b><br/>
        <span>{subTitle}</span>
    </div>;
}

const useStyles = makeStyles(styles);
const Indicators = (props) => {
    const classes = useStyles();
    const [scoring, setScoring] = React.useState({
        dialogs: null,
        callDialogs: null,
        chatDialogs: null,
        activeTime: null,
        serviceLevelCall: null,
        serviceLevelChat: null,
        serviceLevel: null,
        serviceLevelAverageSpeedAnswerChat: null,
        serviceLevelAverageSpeedAnswerCall: null,
        serviceLevelAverageSpeedAnswer: null,
        knowledge: null,
        scoring: null,
        scoringCall: null,
        scoringChat: null,
        scoreCoveringCall:null,
        scoreCoveringChat:null,
        totalScore: null,
    });
    let operatorRanks = null;
    let supervisorRanks = null;
    React.useEffect(() => {
        let active = true;
        dataProvider({
            params: {
                id: props.user.id
            }
        }).get('/users/scoring').then(res => {
            if (active) setScoring(res.data.shift());
        });
        return () => active = false;
    }, []);
    operatorRanks = props?.user?.ranks?.filter(rank => rank.type === 'ROLE_OPERATOR');
    supervisorRanks = props?.user?.ranks?.filter(rank => rank.type === 'ROLE_SUPERVISOR');

    return <>
        <h1>Показатели</h1>
        <Card>
            <CardHeader color="primary">
                <h4 className={classes.cardTitleWhite}>Актуальные</h4>
            </CardHeader>
            <CardBody>
                <Table
                    tableHeaderColor="primary"
                    tableHead={[<Indicator style={{width: "240px"}} title={"Показатели"} style={{textAlign:"center"}}/>,
                        <HeadsetMicIcon/>, <ChatIcon/>, <FunctionsIcon/>, <PieChartIcon/>]}
                >
                    {[
                        [{
                            title: "Количество коммуникаций",
                            subTitle: "Реальная рабочая активность"
                        }, scoring?.callDialogs, scoring?.chatDialogs, scoring?.dialogs, ''],
                        [{
                            title: "Active Time",
                            subTitle: "Реальная рабочая активность"
                        }, null, null, scoring?.activeTime, "0"],
                        [{title: "SL", subTitle: "Доля коммуникаций отработанных своевременно (среднее время тишины)"},
                            scoring?.serviceLevelCall, scoring?.serviceLevelChat, scoring?.serviceLevel, "0"],
                        [{title: "SL ASA", subTitle: "Уровень вашей доступности (среднее время первого ответа)"},
                            scoring?.serviceLevelAverageSpeedAnswerCall, scoring?.serviceLevelAverageSpeedAnswerChat, scoring?.serviceLevelAverageSpeedAnswer, "0"],
                        [{title: "Уровень знаний", subTitle: "Уровень знаний"},
                            '-', '-', scoring?.knowledge, "0.3"],
                        [{title: "Оценка качества", subTitle: "Уровень качества ваших консультаций"},
                            scoring?.scoringCall, scoring?.scoringChat, scoring?.scoring, "0.7"],
                        [{title: "Итоговый рейтинг", subTitle: ""},
                            null, null, null,scoring?.totalScore]
                    ].map((prop, key) => {
                        return (
                            <TableRow key={key} className={classes.tableBodyRow}>
                                {prop.map((prop, key) => {
                                    return (
                                        <TableCell align={"center"} size={'small'} width={"240px"}
                                                   className={classes.tableCell} key={key}>
                                            {key === 0 ?
                                                <Indicator title={prop.title} subTitle={prop.subTitle}/> : prop}
                                        </TableCell>
                                    );
                                })}
                            </TableRow>
                        );
                    })}
                </Table>
            </CardBody>
        </Card>
        <Card>
            <CardHeader color="primary">
                <h4 className={classes.cardTitleWhite}>История</h4>
            </CardHeader>
            {supervisorRanks?.length > 0 && <CardBody>
                <Typography variant="caption" display="inline">Рейтинг супервизора</Typography>
                <Table
                    tableHeaderColor="primary"
                    tableHead={[<Indicator title={"Период"}/>,
                        <Indicator title={"Зона"}/>,
                        <Indicator title={"Позиция"}/>,
                        <Indicator title={"Рейтинг"}/>,
                    ]}
                >
                    {supervisorRanks && supervisorRanks.sort((a, b) => {
                        if (a.date < b.date) {
                            return 1;
                        }
                        if (a.date > b.date) {
                            return -1;
                        }
                        return 0;
                    }).map((item) => {
                        return {
                            color: item?.trainee ? '#ccc' : item.zone.color,
                            data: [
                                moment(item.date, '').format('MM/YYYY'),
                                item?.zone.name,
                                item.position,
                                <div>{item?.score}</div>
                            ]
                        }
                    })
                        .map((prop, key) => {
                            return (
                                <TableRow key={key} className={classes.tableBodyRow} style={{background: prop?.color}}>
                                    {prop.data.map((prop, key) => {
                                        return (
                                            <TableCell align={"center"} size={'small'} width={"240px"}
                                                       className={classes.tableCell} key={key}>
                                                {prop?.title ?
                                                    <Indicator title={prop.title} subTitle={prop.subTitle}/> : prop}
                                            </TableCell>
                                        );
                                    })}
                                </TableRow>
                            );
                        })}
                </Table>
            </CardBody>}
            {operatorRanks?.length > 0 && <CardBody>
                {supervisorRanks?.length > 0 && <Typography variant="caption" display="inline">Рейтинг оператора</Typography>}
                <Table
                    tableHeaderColor="primary"
                    tableHead={[<Indicator title={"Период"}/>,
                        <Indicator title={"Зона"}/>,
                        <Indicator title={"Позиция"}/>,
                        <Indicator title={"Рейтинг"}/>,
                    ]}
                >
                    {operatorRanks && operatorRanks.sort((a, b) => {
                        if (a.date < b.date) {
                            return 1;
                        }
                        if (a.date > b.date) {
                            return -1;
                        }
                        return 0;
                    }).map((item) => {
                        return {
                            color: item?.trainee ? '#ccc' : item.zone.color,
                            data: [
                                moment(item.date, '').format('MM/YYYY'),
                                item?.zone.name,
                                item.position,
                                <div>{item?.score} {item?.trainee && <div style={{color: '#b60000'}}>не рейтингуетесь</div>}</div>
                            ]
                        }
                    })
                        .map((prop, key) => {
                            return (
                                <TableRow key={key} className={classes.tableBodyRow} style={{background: prop?.color}}>
                                    {prop.data.map((prop, key) => {
                                        return (
                                            <TableCell align={"center"} size={'small'} width={"240px"}
                                                       className={classes.tableCell} key={key}>
                                                {prop?.title ?
                                                    <Indicator title={prop.title} subTitle={prop.subTitle}/> : prop}
                                            </TableCell>
                                        );
                                    })}
                                </TableRow>
                            );
                        })}
                </Table>
            </CardBody>}
        </Card>
    </>;
}
export default Indicators;

