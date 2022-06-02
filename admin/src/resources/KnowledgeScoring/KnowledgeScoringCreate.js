import React from 'react';
import {Create, NumberInput, DateInput, required, SimpleForm, TextInput, ReferenceInput, AutocompleteInput, ImageInput, ImageField} from 'react-admin';

const KnowledgeScoringCreate = props => {
    return (<Create {...props} style={{width: "500px"}}>
        <SimpleForm redirect={'list'}>
            <TextInput label={"Название теста"} source={"name"}/>
            <ReferenceInput label="Оператор" source="user" reference="admin/users" filterToQuery={searchText => ({ name: [searchText] })}>
                <AutocompleteInput source="name" validate={[required()]}/>
            </ReferenceInput>
            <DateInput label={"Дата проведения"} source={"date"} validate={[required()]}/>
            <ImageInput source={"screenshot"} label={"Скриншот"} accept="image/*" placeholder={<p>Перетащите скриншот сюда</p>} validate={[required()]}>
                <ImageField source="src" title="title" />
            </ImageInput>
            <NumberInput label={"Результат"} source={"result"}/>
            <NumberInput label={"Коефициент тестированния"} source={"coefficient"} min={0} max={1}/>
        </SimpleForm>
    </Create>);
}

export default KnowledgeScoringCreate;
