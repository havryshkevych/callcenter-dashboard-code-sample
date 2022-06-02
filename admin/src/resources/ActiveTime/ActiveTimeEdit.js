import React from 'react';
import {Edit, NumberInput, DateInput, required, SimpleForm, ReferenceInput, AutocompleteInput} from 'react-admin';

const ActiveTimeEdit = props => {
    return (<Edit {...props} style={{width: "500px"}} hasShow={false}>
        <SimpleForm redirect={'list'}>
            <ReferenceInput label="Оператор" source="user" reference="admin/users" filterToQuery={searchText => ({ name: [searchText] })}>
                <AutocompleteInput source="name" validate={[required()]} disabled/>
            </ReferenceInput>
            <NumberInput label={"Часов"} format={s => {
                return Number((s / 60 / 60 * 100) / 100).toFixed(2);
            } } parse={v => {
                return parseInt(parseFloat(v)  * 60 * 60);
            }} source={"seconds"} validate={[required()]}/>
            <DateInput label={"Дата"} source={"date"} validate={[required()]}/>
        </SimpleForm>
    </Edit>);
}

export default ActiveTimeEdit;
