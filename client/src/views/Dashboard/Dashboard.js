import React, {useEffect, useState} from "react";
// @material-ui/core
import {makeStyles, withStyles} from '@material-ui/core/styles';
import Table from '@material-ui/core/Table';
import TableBody from '@material-ui/core/TableBody';
import {TableCell as Cell} from '@material-ui/core';
import TableContainer from '@material-ui/core/TableContainer';
import TableHead from '@material-ui/core/TableHead';
import TableRow from '@material-ui/core/TableRow';
import Paper from '@material-ui/core/Paper';
import Tooltip from '@material-ui/core/Tooltip';
import IconButton from '@material-ui/core/IconButton';
import HeadsetMicIcon from '@material-ui/icons/HeadsetMic';
import ChatIcon from '@material-ui/icons/Chat';
import FunctionsIcon from '@material-ui/icons/Functions';
import PieChartIcon from '@material-ui/icons/PieChart';
import FullscreenIcon from '@material-ui/icons/Fullscreen';
import FullscreenExitIcon from '@material-ui/icons/FullscreenExit';
// core components
import GridItem from "components/Grid/GridItem.js";
import GridContainer from "components/Grid/GridContainer.js";
import Card from "components/Card/Card.js";
import CardHeader from "components/Card/CardHeader.js";
import CardBody from "components/Card/CardBody.js";
import moment from 'moment';
import {generatePath, Link, useLocation} from "react-router-dom";
import styles from "assets/jss/material-dashboard-react/views/dashboardStyle.js";
import {useInterval} from "../../hooks/useInterval";
import dataProvider from "../../dataProvider";
import {FullScreen, useFullScreenHandle} from "react-full-screen";
import ChartistGraph from "react-chartist";
import Legend from "chartist-plugin-legend";
import LocalLibraryIcon from '@material-ui/icons/LocalLibrary';
import "../../assets/css/chart.css";
import MonthInput from "../../components/CustomInput/MonthInput";

const useStyles = makeStyles(styles);

export default function Dashboard() {
    const handleFS = useFullScreenHandle();
    const handleFSSupervisor = useFullScreenHandle();
    const classes = useStyles();
    let location = useLocation();
    const TableCell = withStyles({
        root: {
            padding: '6px 12px 6px 8px'
        }
    })(Cell);
    const blendColors = (colorA, colorB, amount = 0.5) => {
        colorA = colorA.substring(1).length < 6 ? '#' + colorA.substring(1) + colorA.substring(1) : colorA;
        colorB = colorB.substring(1).length < 6 ? '#' + colorB.substring(1) + colorB.substring(1) : colorB;
        const [rA, gA, bA] = colorA.match(/\w\w/g).map((c) => parseInt(c, 16));
        const [rB, gB, bB] = colorB.match(/\w\w/g).map((c) => parseInt(c, 16));
        const r = Math.round(rA + (rB - rA) * amount).toString(16).padStart(2, '0');
        const g = Math.round(gA + (gB - gA) * amount).toString(16).padStart(2, '0');
        const b = Math.round(bA + (bB - bA) * amount).toString(16).padStart(2, '0');
        return '#' + r + g + b;
    }
    const traineeBackgroundColor = (row) => ({backgroundImage: 'repeating-linear-gradient(-45deg,' + blendColors(row.zone?.color, '#ccc', 0.6) + ',' + blendColors(row.zone?.color, '#ccc', 0.6) + ' 1rem,#ccc 1rem,#ccc 2rem)'});
    const [data, setData] = useState([]);
    const [date, setDate] = useState({
        startOfMonth: moment().startOf('month').format('YYYY-MM-DD HH:mm:ss'),
        endOfMonth: moment().endOf('month').format('YYYY-MM-DD HH:mm:ss')
    });
    const [supervisorData, setSupervisorData] = useState([]);
    const [supervisorDate, setSupervisorDate] = useState({
        startOfMonth: moment().startOf('month').format('YYYY-MM-DD HH:mm:ss'),
        endOfMonth: moment().endOf('month').format('YYYY-MM-DD HH:mm:ss')
    });
    const totalScoringData = {
        series: [30, 70],
        labels: ['Знания', 'Контроль качества', 'Отработаные часы'],
    };
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
    const fetchSupervisorData = async () => {
        await dataProvider({
            params: {
                'dialogs.date[after]': supervisorDate.startOfMonth,
                'dialogs.date[before]': supervisorDate.endOfMonth
            }
        }).get('/users/scoring-supervisors').then(res => {
            setSupervisorData(res.data);
        })
    }
    useEffect(async () => {
        fetchData();
        fetchSupervisorData();
    }, [date, supervisorDate]);

    useInterval(() => {
        fetchData();
        fetchSupervisorData();
    }, 1000 * 60 * 5);

    return (
        <div>
            <GridContainer>
                <GridItem xs={12} sm={12} md={12}>
                    <FullScreen handle={handleFS}>
                        <Card>
                            <CardHeader color="primary">
                                <h4 className={classes.cardTitleWhite}>Статистика сотрудников</h4>
                                <MonthInput setDate={setDate}/>
                                <div style={{position: 'absolute', right: '10px', top: '15px'}}>
                                    {handleFS.active
                                        ? <IconButton onClick={handleFS.exit}>
                                            <FullscreenExitIcon/>
                                        </IconButton>
                                        : <IconButton onClick={handleFS.enter}>
                                            <FullscreenIcon/>
                                        </IconButton>}
                                </div>
                            </CardHeader>
                            <CardBody>
                                <TableContainer component={Paper}>
                                    <Table className={classes.table} size={"medium"} aria-label="rating table">
                                        <TableHead>
                                            <TableRow>
                                                <TableCell/>
                                                <TableCell/>
                                                <TableCell colSpan={3} align={'center'}>Кол-ство
                                                    обращений</TableCell>
                                                <TableCell colSpan={1} align={'center'}>Active Time</TableCell>
                                                <TableCell align={'center'}>Знания</TableCell>
                                                <TableCell colSpan={1} align={'center'}>Контроль качества</TableCell>
                                            </TableRow>
                                            <TableRow>
                                                <TableCell>#</TableCell>
                                                <TableCell align="left">ФИ оператора</TableCell>

                                                <TableCell align="center">
                                                    <Tooltip title="Звонков">
                                                        <HeadsetMicIcon/>
                                                    </Tooltip>
                                                </TableCell>
                                                <TableCell align="center">
                                                    <Tooltip title="Чатов">
                                                        <ChatIcon/>
                                                    </Tooltip>
                                                </TableCell>
                                                <TableCell align="center">
                                                    <Tooltip title="Звонков и чатов">
                                                        <PieChartIcon/>
                                                    </Tooltip>
                                                </TableCell>

                                                <TableCell align="center">
                                                    <Tooltip title="АТ - Часы работы">
                                                        <FunctionsIcon/>
                                                    </Tooltip>
                                                </TableCell>

                                                <TableCell align="center">
                                                    <Tooltip title="Результаты тестированния">
                                                        <LocalLibraryIcon/>
                                                    </Tooltip>
                                                </TableCell>

                                                <TableCell align="center">
                                                    <Tooltip
                                                        title="Средневзвешенное значение оценки качества с учетом количества звонков и чатов"><PieChartIcon/></Tooltip>
                                                </TableCell>

                                                <TableCell align="center"><Tooltip
                                                    title="Результат баллов"><PieChartIcon/></Tooltip></TableCell>
                                            </TableRow>

                                        </TableHead>
                                        <TableBody>
                                            {data.map((row, index) => (
                                                <TableRow key={row.name} className={row.trainee ? "trainee-row" : null}
                                                          style={
                                                              row.trainee
                                                                  ? traineeBackgroundColor(row)
                                                                  : {backgroundColor: row.zone?.color}
                                                          }>
                                                    <TableCell component="th" scope="row">
                                                        {index + 1}
                                                    </TableCell>
                                                    <TableCell align="left" style={{whiteSpace: 'nowrap'}}>
                                                        <Link to={generatePath('/user/:userId', {
                                                            userId: row.id
                                                        })} style={{
                                                            color: '#111111',
                                                            textDecoration: 'none',
                                                            textShadow: '0px 0px 1px black'
                                                        }}
                                                        >{row.name}</Link>
                                                    </TableCell>
                                                    <TableCell align="center">{row.callDialogs}</TableCell>
                                                    <TableCell align="center">{row.chatDialogs}</TableCell>
                                                    <TableCell align="center">{row.dialogs}</TableCell>
                                                    <TableCell align="center">{row.activeTime}</TableCell>
                                                    <TableCell align="center">{row.knowledge}</TableCell>
                                                    <TableCell align="center">
                                                        <Tooltip
                                                            title={"Звонки " + row.scoringCall + " + Чаты " + row.scoringChat + ""}><span>{row.scoring}</span></Tooltip>
                                                    </TableCell>
                                                    <TableCell align="center">{row.totalScore}</TableCell>
                                                </TableRow>
                                            ))}
                                        </TableBody>
                                    </Table>
                                </TableContainer>
                            </CardBody>
                        </Card>
                    </FullScreen>
                </GridItem>
                <GridItem xs={12} sm={6} md={6} style={{minWidth: "530px"}}>
                    <Card chart>
                        <CardHeader color="primary">
                            <h4 style={{margin: 0, padding: 0}}>Из чего состоит итоговый балл</h4>
                            <ChartistGraph
                                className="ct-chart-pie"
                                data={totalScoringData}
                                type="Pie"
                                options={{
                                    requireLabelColor: 'white',
                                    height: '380px',
                                    donut: true,
                                    donutWidth: 90,
                                    donutSolid: true,
                                    startAngle: 170,
                                    showLabel: true,
                                    plugins: [Legend({
                                        clickable: false
                                    })],
                                    labelInterpolationFnc: function (value, index) {
                                        return totalScoringData.series[index] + '%';
                                    }
                                }}
                            />
                        </CardHeader>
                        <CardBody>

                        </CardBody>
                    </Card>
                </GridItem>
                <GridItem xs={12} sm={6} md={6}>
                    <FullScreen handle={handleFSSupervisor}>
                        <Card>
                            <CardHeader color="primary">
                                <h4 className={classes.cardTitleWhite}>Статистика супервизоров</h4>
                                <MonthInput className={classes.cardCategoryWhite} setDate={setSupervisorDate}/>
                                <div style={{position: 'absolute', right: '10px', top: '15px'}}>
                                    {handleFSSupervisor.active
                                        ? <IconButton onClick={handleFSSupervisor.exit}>
                                            <FullscreenExitIcon/>
                                        </IconButton>
                                        : <IconButton onClick={handleFSSupervisor.enter}>
                                            <FullscreenIcon/>
                                        </IconButton>}
                                </div>
                            </CardHeader>
                            <CardBody>
                                <TableContainer component={Paper}>
                                    <Table className={classes.table} size={"medium"} aria-label="rating table">
                                        <TableHead>
                                            <TableRow>
                                                <TableCell>#</TableCell>
                                                <TableCell align="left">ФИ супервизора</TableCell>
                                                <TableCell align="center">
                                                    <Tooltip title="АТ - Часы работы">
                                                        <FunctionsIcon/>
                                                    </Tooltip>
                                                </TableCell>
                                                <TableCell align="center">
                                                    <Tooltip
                                                        title="Контроль качества группы"><PieChartIcon/></Tooltip>
                                                </TableCell>
                                            </TableRow>
                                        </TableHead>
                                        <TableBody>
                                            {supervisorData.map((row, index) => (
                                                <TableRow key={index} style={{backgroundColor: row.zone?.color}}>
                                                    <TableCell component="th" scope="row">
                                                        {index + 1}
                                                    </TableCell>
                                                    <TableCell align="left" style={{whiteSpace: 'nowrap'}}>
                                                        <Link to={generatePath('/user/:userId', {
                                                            userId: row.id
                                                        })} style={{
                                                            color: '#111111',
                                                            textDecoration: 'none',
                                                            textShadow: '0px 0px 1px black'
                                                        }}
                                                        >{row.name}</Link>
                                                    </TableCell>
                                                    <TableCell align="center">{row.activeTime}</TableCell>
                                                    <TableCell align="center">{row.scoringRatio}</TableCell>
                                                </TableRow>
                                            ))}
                                        </TableBody>
                                    </Table>
                                </TableContainer>
                            </CardBody>
                        </Card>
                    </FullScreen>
                </GridItem>
            </GridContainer>
        </div>
    );
}
