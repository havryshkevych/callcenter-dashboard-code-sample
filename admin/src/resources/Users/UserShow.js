import React from 'react';
import {DateField, ImageField, Show, SimpleShowLayout, TextField, BooleanField} from 'react-admin';

const UserShow = props => {
    return (<Show {...props}>
        <SimpleShowLayout>
            <ImageField source={"photo"} title={"name"}/>
            <TextField source={"email"} addLabel={true} type={"email"}/>
            <TextField source={"name"} addLabel={true}/>
            <TextField source={"description"} addLabel={true}/>
            <TextField label={"Call ID"} source={"callId"} addLabel={true}/>
            <TextField label={"Chat ID"} source={"chatId"} addLabel={true}/>
            <DateField label={"Начинает работу с"} source={"workStartAt"}/>
            <BooleanField source={"active"} label={"Рейтингуется"}/>
        </SimpleShowLayout>
    </Show>)
};


export default UserShow;