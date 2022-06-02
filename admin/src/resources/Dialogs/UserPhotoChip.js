import React from 'react';
import Chip from '@material-ui/core/Chip';
import Avatar from '@material-ui/core/Avatar';

const UserPhotoChip = props => {
    return <Chip style={{
        margin: "5px",
        height: "70px",
        cursor: "pointer",
        borderTopLeftRadius: "40px",
        borderBottomLeftRadius: "40px"
    }} avatar={props.record[props.avatar] ? <Avatar style={{width:"64px", height: "64px"}} alt="" src={props.record[props.avatar]} /> : ''}
                 label={props.record[props.source]}/>
}

export default UserPhotoChip;