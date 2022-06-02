import React from 'react';
import {Filter, NullableBooleanInput, SelectInput} from 'react-admin';


export default props => <Filter {...props}>
    <SelectInput label={"Канал"} source="type" choices={[
        {id: '', name: ''},
        {id: 'chat', name: 'Чат'},
        {id: 'call', name: 'Телефон'},
    ]} alwaysOn/>
    <NullableBooleanInput label={"Критическая"} source={"critical"} alwaysOn/>
    <NullableBooleanInput label={"Активная"} source={"active"} alwaysOn/>
</Filter>
