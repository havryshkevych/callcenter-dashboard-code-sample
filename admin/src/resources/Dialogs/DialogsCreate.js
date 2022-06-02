import React from 'react';
import {Create, required, SimpleForm, NumberInput, ReferenceInput, DateTimeInput, AutocompleteInput, FileInput, TextInput} from 'react-admin';
import AudioField from "../../helper/AudioField";

const DialogsCreate = props => {
    return (<Create {...props} style={{width: "500px"}}  resource={"dialog/import-call"} >
        <SimpleForm redirect={'list'}>
            <TextInput source={"clientId"}/>
            <ReferenceInput label="Оператор" source="user" reference="admin/users" filterToQuery={searchText => ({ name: [searchText] })} >
                <AutocompleteInput source="name" validate={[required()]}/>
            </ReferenceInput>
            <DateTimeInput source={"receivedAt"} label={"Время начала диалога"} validate={[required()]} />
            <FileInput source={"records"} label={"Добавить звонок"} accept="audio/*" multiple={true} placeholder={<p>Перетащите файлы сюда</p>} validate={[required()]}>
                <AudioField source={"src"} showTime/>
            </FileInput>
            <NumberInput source={"duration"} label={"Продолжительность (секунды)"} validate={[required()]}/>
        </SimpleForm>
    </Create>);
}

export default DialogsCreate;
