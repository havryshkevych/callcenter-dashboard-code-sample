import React from "react";
import {Datagrid, EditButton, List, ShowButton, TextField, ImageField, DateField, BooleanField} from "react-admin";
import UserFilter from "./UserFilter";
import RoleTypeField from "../../components/RoleTypeField";
import Avatar from "@material-ui/core/Avatar";

const UserAvatar = props => {
    return <Avatar style={{width:"128px", height: "128px"}} alt="" src={props.record[props.source]} />
}

const UserList = props => {
    return (<List bulkActionButtons={false} exporter={false} filters={<UserFilter/>} {...props}>
        <Datagrid>
            <RoleTypeField addLabel={true} label={"Type"} source={"roles"}/>
            <UserAvatar source={"photo"}/>
            <TextField source={"email"} type={"email"}/>
            <TextField source={"name"}/>
            <TextField source={"description"}/>
            <DateField source={"workStartAt"}/>
            <BooleanField source={"active"} label={"Рейтингуется"}/>
            <ShowButton/>
            <EditButton/>
        </Datagrid>
    </List>);
};

export default UserList;