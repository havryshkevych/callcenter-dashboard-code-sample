import React from 'react';
import {Edit, NumberInput, DateInput, required, SimpleForm, TextInput, ReferenceInput, AutocompleteInput} from 'react-admin';
import ImageLink from "../../helper/ImageLink";

const KnowledgeScoringEdit = props => {
    return (<Edit {...props} style={{width: "500px"}} hasShow={false} hasDelete={true} mutationMode={'pessimistic'}>
        <SimpleForm redirect={'list'}>
            <TextInput label={"Название теста"} source={"name"}/>
            <ReferenceInput label="Оператор" source="user" reference="admin/users" filterToQuery={searchText => ({ name: [searchText] })}>
                <AutocompleteInput source="name" disabled/>
            </ReferenceInput>
            <DateInput label={"Дата проведения"} source={"date"} validate={[required()]}/>
            <ImageLink source={"screenshot"} label={"Скриншот"}/>
            <NumberInput label={"Результат"} source={"result"}/>
            <NumberInput label={"Коефициент тестированния"} source={"coefficient"} min={0} max={1}/>
        </SimpleForm>
    </Edit>);
}

export default KnowledgeScoringEdit;
