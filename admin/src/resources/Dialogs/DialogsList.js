import React from 'react';
import {Datagrid, ReferenceArrayField, SingleFieldList, DateField, EditButton, List, ReferenceField, TextField, BooleanField, NumberField} from 'react-admin';
import DialogsFilter from "./DialogsFilter";
import DurationField from "../../components/DurationField";
import ListActions from "./ListActions";
import DialogTypeField from "../../helper/DialogTypeField";
import UserPhotoChip from "./UserPhotoChip";

const DialogList = props => {
    const IvertedBooleanField = props => {
        let record = {...props.record};
        record[props.source] = !record[props.source];
        return <BooleanField {...props} record={record}/>
    }
    const CountField = props => {
        return <span>{props.record[props.source].length}</span>
    }
    return (<List {...props} actions={<ListActions/>} filters={<DialogsFilter />} filterDefaultValues={{"exists[user]": true}}  bulkActionButtons={false} exporter={false} hasCreate={true}>
        <Datagrid>
            <DialogTypeField source={"type"} />
            <ReferenceArrayField label="Operators" reference="admin/users" source="users">
                <SingleFieldList style={{margin:0}}>
                    <UserPhotoChip source="name" avatar={"photo"} />
                </SingleFieldList>
            </ReferenceArrayField>
            <DateField source={"date"} showTime={true}/>
            <DurationField source={"duration"} label={"Продолжительность диалога"}/>
            <DurationField source={"firstAnswerSpeed"} label={"Время первого ответа"}/>
            <DurationField source={"averageSpeedAnswer"} label={"Среднее время ответа"}/>
            <IvertedBooleanField source={"serviceLevelWarning"} label={"SL"}/>
            <IvertedBooleanField source={"serviceLevelAverageAnswerSpeedWarning"} label={"SL ASA"}/>
            <CountField label={'Оцениваний'} source={'scoring'}/>
            <EditButton label="Подробнее"/>
        </Datagrid>
    </List>);
}

export default DialogList;