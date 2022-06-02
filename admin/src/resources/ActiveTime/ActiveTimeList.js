import React from 'react';
import {Datagrid, DateField, EditButton, List, ReferenceField, TextField } from 'react-admin';
import ActiveTimeFilter from "./ActiveTimeFilter";

const HourField = ({record, source}) => {
    return <span>{Number(record[source] / 60 / 60).toFixed(2)}</span>
}

const ActiveTimeList = props => {
    return (<List {...props} filters={<ActiveTimeFilter />} bulkActionButtons={false} exporter={false} hasCreate={true}>
        <Datagrid>
            <ReferenceField label="User" source="user" reference="admin/users">
                <TextField source="name"/>
            </ReferenceField>
            <DateField source={"date"} showTime={false}/>
            <HourField source={"seconds"} label={"Часов"}/>
            <EditButton label="Edit"/>
        </Datagrid>
    </List>);
}

export default ActiveTimeList;
