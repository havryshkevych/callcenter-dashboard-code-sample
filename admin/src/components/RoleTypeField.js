import React from 'react';
import Tooltip from '@material-ui/core/Tooltip';
import PhoneCallbackIcon from '@material-ui/icons/PhoneCallback';
import MergeTypeIcon from '@material-ui/icons/MergeType';

const RoleTypeField = props => {
    let role = '';
    if (Array.isArray(props.record[props.source])) {
        role = props.record[props.source].includes('ROLE_SUPERVISOR') ? 'ROLE_SUPERVISOR' : (props.record[props.source].includes('ROLE_OPERATOR') ? 'ROLE_OPERATOR' : '');
    } else {
        role = props.record[props.source];
    }
    if (role === 'ROLE_SUPERVISOR') {
        return <Tooltip title={role}>
            <MergeTypeIcon/>
        </Tooltip>;
    }
    if (role === 'ROLE_OPERATOR') {
        return <Tooltip title={role}>
            <PhoneCallbackIcon/>
        </Tooltip>;
    }
    return <span/>
}

export default RoleTypeField;