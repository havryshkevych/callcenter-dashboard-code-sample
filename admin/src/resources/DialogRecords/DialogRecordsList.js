import React from 'react';
import {Datagrid, DateField, List, NumberField, TextField} from 'react-admin';

const DialogRecordList = props => {
    return (
        <List {...props} bulkActionButtons={false} exporter={false} hasCreate={false}>
            <Datagrid>
                <TextField source={"chatId"}/>
                <DateField source={"receivedAt"}/>
                <NumberField source={"seconds"}/>
                <TextField source={"sender"}/>
                <DateField source={"createdAt"}/>
                <DateField source={"updatedAt"}/>
            </Datagrid>
        </List>);
}

export default DialogRecordList;