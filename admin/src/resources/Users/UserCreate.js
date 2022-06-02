import React from 'react';
import {
    Create,
    required,
    SimpleForm,
    TextInput,
    DateTimeInput,
    BooleanInput,
    SelectArrayInput,
    ReferenceArrayInput,
    AutocompleteArrayInput,
    ReferenceInput,
    AutocompleteInput,
    ImageInput,
    ImageField
} from 'react-admin';
import {makeStyles} from '@material-ui/core/styles';

const useStyles = makeStyles({
    dropZone: {
        width: "256px"
    },
    preview: {},
    removeButton: {},
});

const UserCreate = props => {
    const classname = useStyles();
    const transform = data => {
        if (data.roles) {
            if (data.roles.includes("ROLE_OPERATOR") && !data.roles.includes("ROLE_SUPERVISOR")) {
                data.operators = [];
            }
            if (data.roles.includes("ROLE_SUPERVISOR") && !data.roles.includes("ROLE_OPERATOR")) {
                data.supervisor = null;
            }
        } else {
            data.operators = [];
            data.supervisor = null;
        }
        return data;
    };

    return <Create {...props} transform={transform}>
        <SimpleForm redirect={'edit'}>
            <TextInput source={"email"} type={"email"} validate={[required()]}/>
            <TextInput source={"name"} validate={[required()]}/>
            <ImageInput source="photo" label="Upload new photo" accept="image/*" multiple={false}
                        placeholder={<p>Drop your file here</p>} formClassName={classname.dropZone}>
                <ImageField source="src"/>
            </ImageInput>
            <TextInput source={"description"}/>
            <SelectArrayInput source="roles" choices={[
                {id: 'ROLE_OPERATOR', name: 'Operator'},
                {id: 'ROLE_SUPERVISOR', name: 'Supervisor'}
            ]}/>
            <ReferenceArrayInput source="operators" filter={{roles: "ROLE_OPERATOR"}} reference="admin/users">
                <AutocompleteArrayInput optionText="name"/>
            </ReferenceArrayInput>
            <ReferenceInput label="Supervisor" filter={{roles: "ROLE_SUPERVISOR"}} source="supervisor"
                            reference="admin/users">
                <AutocompleteInput optionText="name"/>
            </ReferenceInput>
            <TextInput label={"Callcenter Operator ID"} source={"callId"}/>
            <TextInput label={"Sender Operator ID"} source={"chatId"}/>
            <DateTimeInput label={"Начинает работу с"} source={"workStartAt"}/>
            <BooleanInput label={"Отображать в рейтинговании"} source={"active"}/>
        </SimpleForm>
    </Create>;
};

export default UserCreate;