import React from 'react';
import {Create, NumberInput, DateInput, required, SimpleForm, ReferenceInput, AutocompleteInput} from 'react-admin';

const ActiveTimeCreate = props => {
    return (<Create {...props} style={{width: "500px"}}>
        <SimpleForm redirect={'edit'}>
            <ReferenceInput label="Оператор" source="user" reference="admin/users" filterToQuery={searchText => ({ name: [searchText] })}>
                <AutocompleteInput source="name" validate={[required()]}/>
            </ReferenceInput>
            <NumberInput label={"Часов"} format={s => s / 60 / 60} parse={v => parseInt(parseFloat(v) * 60 * 60)} source={"seconds"} validate={[required()]}/>
            <DateInput label={"Дата"} source={"date"} validate={[required()]}/>
        </SimpleForm>
    </Create>);
}

export default ActiveTimeCreate;
