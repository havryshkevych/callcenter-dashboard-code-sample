import React from "react";
import {BooleanField, Datagrid, EditButton, List, NumberField, TextField} from "react-admin";
import CreateriasFilter from "./CreateriasFilter";

const CreateriasList = props => {
    return (<List {...props} bulkActionButtons={false} exporter={false} hasShow={false} filters={<CreateriasFilter/>}>
        <Datagrid>
            <NumberField label={"Порядок сортировки"} source={"sort"} fullWidth/>
            <BooleanField label={"Критическая"} source={"critical"} fullWidth/>
            <BooleanField label={"Активная"} source={"active"} fullWidth/>
            <TextField label={"Канал"} source={"type"}/>
            <TextField label={"Название"} source={"title"}/>
            <TextField label={"Описание"} source={"description"}/>
            <NumberField label={"Максимальное значение"} source={"max"} fullWidth/>
            <EditButton/>
        </Datagrid>
    </List>);
};

export default CreateriasList;


