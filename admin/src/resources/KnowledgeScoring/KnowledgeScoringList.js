import React from 'react';
import {Datagrid, DateField, EditButton, List, ReferenceField, TextField, NumberField} from 'react-admin';
import KnowledgeScoringFilter from "./KnowledgeScoringFilter";
import DeleteButtonWithConfirm from "../../helper/DeleteButtonWithConfirm";
import ImageLink from "../../helper/ImageLink";


const KnowledgeScoringList = props => {
    return (<List {...props} filters={<KnowledgeScoringFilter />} bulkActionButtons={false} exporter={false} hasCreate={true}>
        <Datagrid>
            <ReferenceField label="User" source="user" reference="admin/users">
                <TextField source="name"/>
            </ReferenceField>
            <TextField label={"Название теста"} source={"name"}/>
            <DateField label={"Дата проведения"} source={"date"} showTime={false}/>
            <NumberField label={"Результат"} source={"result"}/>
            <ImageLink source={"screenshot"}/>
            <NumberField label={"Коефициент тестированния"} source={"coefficient"}/>
            <EditButton label={""}/>
            <DeleteButtonWithConfirm label=""/>
        </Datagrid>
    </List>);
}

export default KnowledgeScoringList;

