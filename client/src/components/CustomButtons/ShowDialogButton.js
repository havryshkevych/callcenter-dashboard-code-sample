import React from 'react';
import Popover from '@material-ui/core/Popover';
import Button from '@material-ui/core/Button';
import VisibilityIcon from '@material-ui/icons/Visibility';
import Card from '@material-ui/core/Card';
import CardContent from '@material-ui/core/CardContent';
import Typography from '@material-ui/core/Typography';
import Tooltip from '@material-ui/core/Tooltip';
import Box from '@material-ui/core/Box';
import ReportProblemOutlinedIcon from '@material-ui/icons/ReportProblemOutlined';
import ContactSupportIcon from '@material-ui/icons/ContactSupport';
import moment from 'moment';

const ShowDialogButton = (props) =>  {
    const [anchorEl, setAnchorEl] = React.useState(null);

    const handleClick = (event) => {
        setAnchorEl(event.currentTarget);
    };

    const handleClose = () => {
        setAnchorEl(null);
    };

    const open = Boolean(anchorEl);
    const id = open ? 'simple-popover' : undefined;
    const showScore = scoring => {
        let hasCritical = scoring.evaluations.filter(evaluation => evaluation?.criteria?.critical && evaluation?.value);
        if (hasCritical.length) {
            return <span style={{color:'#a10000'}}>обнуленно</span>;
        }
        return parseInt(scoring?.score);
    }
    return (
        <div>
            <Button
                aria-describedby={id}
                onClick={handleClick}
                style={{color:'#757575'}}
                startIcon={<VisibilityIcon />}
            >
                Подробнее
            </Button>
            <Popover
                id={id}
                open={open}
                anchorEl={anchorEl}
                onClose={handleClose}
                anchorOrigin={{
                    vertical: 'center',
                    horizontal: 'left',
                }}
                transformOrigin={{
                    vertical: 'center',
                    horizontal: 'right',
                }}
            >
                <Card>
                    <CardContent>
                        <Box p="1em">
                            <Box display="flex">
                                <Box flex={1} mr="1em">
                                    <Typography variant="h6" gutterBottom>Диалог</Typography>
                                    <Box display="flex" style={{flexDirection: "column"}}>
                                        <Box>
                                            <Typography variant="subtitle2" display="inline">ID: </Typography>
                                            {props?.record?.id ? props?.record?.id : '-'}
                                        </Box>
                                        <Box>
                                            <Typography variant="subtitle2" display="inline">Время начала
                                                диалога: </Typography>
                                            {props?.record?.date ? moment(props?.record?.date, '').format('DD-MM HH:mm:ss') : '-'}
                                        </Box>
                                        <Box>
                                            <Typography variant="subtitle2" display="inline">Тип: </Typography>
                                            {props?.record?.type ? props?.record?.type : '-'}
                                        </Box>
                                        <Box>
                                            <Typography variant="subtitle2" display="inline">Продолжительность
                                                диалога: </Typography>
                                            {props?.record?.duration ? moment.utc(props?.record?.duration * 1000).format('HH:mm:ss') : '-'}
                                        </Box>
                                        <Box>
                                            <Typography variant="subtitle2" display="inline">Время до первого
                                                ответа: </Typography>
                                            {props?.record?.firstAnswerSpeed ? moment.utc(props?.record?.firstAnswerSpeed * 1000).format('HH:mm:ss') : '-'}
                                        </Box>
                                        <Box>
                                            <Typography variant="subtitle2" display="inline">Среднее время ответа
                                                оператора: </Typography>
                                            {props?.record?.averageSpeedAnswer ? moment.utc(props?.record?.averageSpeedAnswer * 1000).format('HH:mm:ss') : '-'}
                                        </Box>
                                        {props?.record?.recordsUrl &&
                                        <Box style={{
                                            display: 'flex',
                                            flexDirection: 'row',
                                            flexWrap: 'wrap',
                                            justifyContent: 'flex-start',
                                            alignContent: 'stretch',
                                            alignItems: 'baseline'
                                        }}>
                                            <Typography variant="subtitle2" style={{lineHeight: "36px"}} display="inline">Записи: </Typography>
                                            {props.record.type === 'chat' && props.record.recordsUrl.map((url, index) => {
                                                return <Button key={index} size="small" variant="outlined" color="primary" href={process.env.REACT_APP_CHAT_RECORD_BASE_URL+url}
                                                               target={"_blank"} style={{margin: "5px", textTransform: 'unset'}}>
                                                    Посмотреть диалог {index > 0 && " #" + (index + 1)}
                                                </Button>
                                            })}
                                            {props.record.type === 'call' && props.record.recordsUrl.map((url, index) => {
                                                return <audio controls src={url} key={index}>
                                                    <p>Ваш браузер не поддерживает HTML5 аудио. Вот взамен
                                                        <a href={url} target={"_blank"}>ссылка на аудио</a></p>
                                                </audio>
                                            })}
                                        </Box>}
                                    </Box>
                                </Box>
                                {props.userScoring?.evaluations?.length > 0 && <Box flex={2} ml="1em">
                                    <Typography variant="h6" gutterBottom>Оценивание <span>{props.userScoring && showScore(props.userScoring)}</span></Typography>
                                    {props.userScoring?.evaluations.map((evaluation, index) => {
                                        return <Box id={evaluation.id} key={index} style={{borderBottom:'1px dashed #b3b3b3',
                                            display: 'flex',
                                            flexDirection: 'row',
                                            alignContent: 'center',
                                            justifyContent: 'space-between',
                                            alignItems: 'flex-end'}}>
                                            <Typography variant="subtitle2" display="inline" style={{
                                                color: evaluation.criteria?.critical ? '#500000' : '#000'
                                            }}>{evaluation.criteria?.title}</Typography>
                                            <Typography variant="subtitle2" display="inline" style={{
                                                color: evaluation.criteria?.critical ? '#500000' : '#000',
                                                alignItems: 'flex-end',
                                                display: 'flex'
                                            }}>

                                                {evaluation.criteria?.critical && evaluation.value === 1 && <ReportProblemOutlinedIcon/>}
                                                {!evaluation.criteria?.critical && evaluation.value}
                                                {evaluation?.comment?.length > 0 && <Tooltip title={evaluation.comment} aria-label="info">
                                                    <ContactSupportIcon style={{color: '#2199BE'}}/>
                                                </Tooltip>}
                                            </Typography>
                                        </Box>
                                    })}
                                </Box>}
                            </Box>
                            {props.userScoring?.evaluations?.length > 0 && <Box display={"flex"}>
                                <Box flex={3}>
                                    <Typography variant="h6" gutterBottom>Комментарии к оцениванию</Typography>
                                    {props.userScoring?.evaluations.map((evaluation, index) => {
                                        if (evaluation.comment === '') { return null; }
                                        return <Box id={evaluation.id} key={index} style={{borderBottom:'1px dashed #b3b3b3',
                                            display: 'flex',
                                            flexDirection: 'row',
                                            alignContent: 'left'}}>
                                            <Typography variant="subtitle2" display="inline" style={{
                                                color: evaluation.criteria?.critical ? '#500000' : '#000',
                                            }}>{evaluation.comment}</Typography>
                                        </Box>
                                    })}
                                </Box>
                            </Box>}
                        </Box>
                    </CardContent>
                </Card>
            </Popover>
        </div>
    );
}

export default ShowDialogButton;