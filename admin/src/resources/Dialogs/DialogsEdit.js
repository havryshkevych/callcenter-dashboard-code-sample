import React from 'react';
import {DateField, Edit, FormWithRedirect, TextField, useDataProvider, DeleteButton, useRefresh, ReferenceArrayField,
    SingleFieldList} from 'react-admin';
import ScoringsEdit from "../Scorings/ScoringsEdit";
import {Box, Button, Typography} from '@material-ui/core';
import DurationField from "../../components/DurationField";
import AudioField from "../../helper/AudioField";
import DialogTypeField from "../../helper/DialogTypeField";
import UserPhotoChip from "./UserPhotoChip";

const DialogEvaluations = props => {
    const dataProvider = useDataProvider();
    const refresh = useRefresh()
    const CreateScoringButton = () => {
        return <>
            <Button variant="contained" color="primary" onClick={createScoring} fullWidth>
                Добавить оценивание
            </Button>
        </>;
    }
    const createScoring = async () => {
        const {data} = await dataProvider.getList('admin/criterias', {
            pagination: {page: 1, perPage: 99},
            sort: {field: 'sort', order: 'ASC'},
            filter: {active: true, type: props.record.type},
        });
        const newScoring = {
            dialog: props.record.id,
            evaluations: data.map(criteria => ({
                criteria: criteria.id,
                value: 0,
                comment: ""
            }))
        };

        dataProvider.create('admin/scorings', {data: newScoring}).then(() => refresh())
    }

    return (<>
        {props.record.scoring.map(scoringRecord => {
            return <ScoringsEdit users={props.record.users} key={scoringRecord} id={scoringRecord} basePath={'admin/criterias'} resource={'admin/criterias'} title={" "}/>;
        })}
        <CreateScoringButton record={props.record}/>
    </>);
}



const DialogForm = ({save, ...props}) => (
    <FormWithRedirect
        {...props}
        render={formProps => {
            return (
                <form>
                    <Box p="1em">
                        <Box display="flex">
                            <Box flex={1} mr="1em">
                                <Typography variant="h6" gutterBottom>Диалог</Typography>
                                <Box display="flex" style={{flexDirection: "column"}}>
                                    <Box>
                                        <Typography variant="subtitle2" display="inline">Операторы: </Typography>
                                        <ReferenceArrayField label=" " reference="admin/users" source="users">
                                            <SingleFieldList style={{margin:0}}>
                                                <UserPhotoChip source="name" avatar={"photo"} />
                                            </SingleFieldList>
                                        </ReferenceArrayField>
                                    </Box>
                                    <Box>
                                        <Typography variant="subtitle2" display="inline">ID: </Typography>
                                        <TextField source={"id"}/>
                                    </Box>
                                    <Box>
                                        <Typography variant="subtitle2" display="inline">Время начала
                                            диалога: </Typography>
                                        <DateField label={"Время начала диалога"} source={"date"} showTime/>
                                    </Box>
                                    <Box>
                                        <Typography variant="subtitle2" display="inline">Тип: </Typography>
                                        <DialogTypeField source={"type"} {...props}/>
                                    </Box>
                                    <Box>
                                        <Typography variant="subtitle2" display="inline">Продолжительность
                                            диалога: </Typography>
                                        <DurationField source={"duration"}/>
                                    </Box>
                                    <Box>
                                        <Typography variant="subtitle2" display="inline">Время до первого
                                            ответа: </Typography>
                                        <DurationField source={"firstAnswerSpeed"}/>
                                    </Box>
                                    <Box>
                                        <Typography variant="subtitle2" display="inline">Среднее время ответа
                                            оператора: </Typography>
                                        <DurationField source={"averageSpeedAnswer"}/>
                                    </Box>
                                    {formProps.record.recordsUrl &&
                                    <Box style={{
                                        display: 'flex',
                                        flexDirection: 'row',
                                        flexWrap: 'wrap',
                                        justifyContent: 'flex-start',
                                        alignContent: 'stretch',
                                        alignItems: 'baseline'
                                    }}>
                                        <Typography variant="subtitle2" style={{lineHeight: "36px"}} display="inline">Записи: </Typography>
                                        {formProps?.record?.recordsUrl?.map((url, index) => {
                                            if (formProps.record.type === 'call') {
                                                return <AudioField key={index} record={{url:url}} source={"url"}/>
                                            }
                                            return <Button key={index} size="small" variant="outlined" color="primary" href={process.env.REACT_APP_CHAT_RECORD_BASE_URL + url}
                                                           target={"_blank"} style={{margin: "5px", textTransform: 'unset'}}>
                                                Посмотреть диалог {index > 0 && " #" + (index + 1)} {url && '('+url+')'}
                                            </Button>
                                        })}
                                    </Box>}
                                </Box>
                                <Box display="flex">
                                    <DeleteButton mutationMode="pessimistic" {...props} />
                                </Box>
                            </Box>
                            <Box flex={2} ml="1em">
                                <Typography variant="h6" gutterBottom>Оценивание</Typography>
                                <DialogEvaluations {...formProps}/>
                            </Box>
                        </Box>
                    </Box>
                </form>
            );
        }
        }
    />
);

const DialogsEdit = props => {

    return (<Edit {...props} hasShow={false} undoable={false} title={"Оцениевание диалога"}>
            <DialogForm/>
        </Edit>
    );
}
export default DialogsEdit;
