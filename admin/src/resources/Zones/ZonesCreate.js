import React from 'react';
import {BooleanInput, Create, NumberInput, SimpleForm, TextInput, SelectInput} from 'react-admin';
import ColorPickerInput from "../../components/ColorPickerInput";
import RichTextInput from 'ra-input-rich-text';
import RangeSlider from "../../components/RangeSlider";

const ZonesCreate = props => (
    <Create {...props} style={{width: "500px"}}>
        <SimpleForm redirect={'edit'}>
            <TextInput source={"name"} fullWidth />
            <ColorPickerInput source={"color"} label={"Цвет"}/>
            <RichTextInput source="description" stripTags fullWidth />
            <RichTextInput source="hint" stripTags fullWidth />
            <NumberInput source="rangeStart" fullWidth />
            <NumberInput source="rangeEnd" fullWidth />
            <RangeSlider source={["rangeStart", "rangeEnd"]}/>
            <NumberInput source="priority" fullWidth />
            <SelectInput source="type" choices={[
                { id: 'ROLE_OPERATOR', name: 'Operator' },
                { id: 'ROLE_SUPERVISOR', name: 'Supervisor' },
            ]} />
            <BooleanInput label={"Активная"} source={"active"} fullWidth/>
        </SimpleForm>
    </Create>
);

export default ZonesCreate;