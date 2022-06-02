import React from 'react';
import {Filter, TextInput} from 'react-admin';


export default props => <Filter {...props}>
    <TextInput alwaysOn source={"email"} label={"Email"}/>
    <TextInput alwaysOn source={"name"} label={"ФИО"}/>
    <TextInput source={"chatId"} label={"Call ID"}/>
    <TextInput source={"callId"} label={"Call ID"}/>
</Filter>
