import React from 'react';
import {BooleanInput, Edit, NumberInput, RadioButtonGroupInput, required, SimpleForm, TextInput} from 'react-admin';

const CreateriasEdit = props => {
    return (
        <Edit {...props} hasShow={false}>
            <SimpleForm redirect={false}>
                <RadioButtonGroupInput label={"Канал"} source="type" choices={[
                    {id: 'chat', name: 'Чат'},
                    {id: 'call', name: 'Телефон'},
                ]} initialValue={"chat"} fullWidth disabled validate={[required()]}/>
                <TextInput label={"Название"} source={"title"} fullWidth validate={[required()]}/>
                <TextInput label={"Описание"} source={"description"} fullWidth/>
                <NumberInput label={"Максимальное значение"} source={"max"} fullWidth/>
                <NumberInput label={"Порядок сортировки"} source={"sort"} fullWidth/>
                <BooleanInput label={"Критическая"} source={"critical"} fullWidth/>
                <BooleanInput label={"Активная"} source={"active"} fullWidth/>
            </SimpleForm>
        </Edit>
    );
}

export default CreateriasEdit;