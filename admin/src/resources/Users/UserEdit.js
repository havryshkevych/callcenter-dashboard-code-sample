import React from 'react';
import {
    Edit,
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
import Avatar from "@material-ui/core/Avatar";
import { makeStyles } from '@material-ui/core/styles';

const useStyles = makeStyles({
    dropZone: {
        width: "256px"
    },
    preview: {

    },
    removeButton: {

    },
});
const UserAvatar = props => {
    return <Avatar style={{width:"256px", height: "256px"}} alt="" src={props.record[props.source]} />
}
const UserEdit = props => {
    const classname = useStyles();
    const transform = data => {
        if (data.roles.includes("ROLE_OPERATOR") && !data.roles.includes("ROLE_SUPERVISOR")) {
            data.operators = [];
        }
        if (data.roles.includes("ROLE_SUPERVISOR") && !data.roles.includes("ROLE_OPERATOR")) {
            data.supervisor = null;
        }
        return data;
    };

    return <Edit {...props} transform={transform} mutationMode={"pessimistic"}>
        <SimpleForm redirect={false}>
            <UserAvatar source="photo" />
            <TextInput source={"email"} type={"email"} disabled/>
            <TextInput source={"name"} validate={[required()]}/>
            <ImageInput source="photo" label="Upload new photo" accept="image/*" multiple={false} placeholder={<p>Drop your file here</p>} formClassName={classname.dropZone} >
                <ImageField source="src" />
            </ImageInput>
            <TextInput source={"description"}/>
            <TextInput label={"Call ID"} source={"callId"}/>
            <TextInput label={"Chat ID"} source={"chatId"}/>
            <SelectArrayInput source="roles" choices={[
                {id: 'ROLE_OPERATOR', name: 'Operator'},
                {id: 'ROLE_SUPERVISOR', name: 'Supervisor'},
            ]}/>
            <ReferenceArrayInput source="operators" filter={{roles: "ROLE_OPERATOR"}} reference="admin/users">
                <AutocompleteArrayInput optionText="name"/>
            </ReferenceArrayInput>
            <ReferenceInput label="Supervisor" filter={{roles: "ROLE_SUPERVISOR"}} source="supervisor"
                            reference="admin/users">
                <AutocompleteInput optionText="name"/>
            </ReferenceInput>
            <DateTimeInput label={"Начинает работу с"} source={"workStartAt"}/>
            <BooleanInput label={"Отображать в рейтинговании"} source={"active"}/>
        </SimpleForm>
    </Edit>;
}

export default UserEdit;