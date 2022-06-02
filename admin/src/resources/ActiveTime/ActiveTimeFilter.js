import React from 'react';
import {Filter, AutocompleteInput, ReferenceInput, DateTimeInput} from 'react-admin';


export default props => <Filter {...props}>
    <ReferenceInput label="Оператор" source="user" reference="admin/users"  alwaysOn allowEmpty filterToQuery={searchText => ({ name: [searchText] })}>
        <AutocompleteInput source="name" />
    </ReferenceInput>
    <DateTimeInput label="дата[после]" source="date[after]" />
    <DateTimeInput label="дата[до]" source="date[before]" />
</Filter>