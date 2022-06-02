import React from 'react';
import {Datagrid, RichTextField, EditButton, List, TextField, NumberField} from 'react-admin';
import { ColorField } from 'react-admin-color-input';
import RoleTypeField from "../../components/RoleTypeField";

const ZonesList = props => {
    return (<List {...props} bulkActionButtons={false} exporter={false} hasCreate={true} >
        <Datagrid>
            <RoleTypeField addLabel={true} label={"Type"} source={"type"}/>
            <TextField source={"name"}/>
            <ColorField source={"color"} label={"Цвет"}/>
            <RichTextField source="description" stripTags />
            <RichTextField source="hint" stripTags />
            <NumberField source="rangeStart"/>
            <NumberField source="rangeEnd"/>
            <NumberField source="priority"/>
            <EditButton/>
        </Datagrid>
    </List>);
}

export default ZonesList;