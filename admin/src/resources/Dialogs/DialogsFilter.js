import React from 'react';
import {Filter, NullableBooleanInput, SelectInput, AutocompleteInput, ReferenceInput, DateTimeInput, TextInput} from 'react-admin';


export default props => <Filter {...props}>
    <TextInput source={"records.session"} alwaysOn label={"ChatID"}/>
    <SelectInput label={"Канал"} source="type" choices={[
        {id: '', name: ''},
        {id: 'chat', name: 'Чат'},
        {id: 'call', name: 'Телефон'},
    ]} alwaysOn/>
    <ReferenceInput label="Оператор" source="users" reference="admin/users" alwaysOn allowEmpty filterToQuery={searchText => ({ name: [searchText] })}>
        <AutocompleteInput optionText="name" />
    </ReferenceInput>
    <DateTimeInput label="дата[после]" source="date[after]" />
    <DateTimeInput label="дата[до]" source="date[before]" />
    <NullableBooleanInput label={"Оценивание"} source={"exists[scoring]"} alwaysOn/>
    <NullableBooleanInput label={"С оператором"} source={"exists[user]"} alwaysOn/>
</Filter>