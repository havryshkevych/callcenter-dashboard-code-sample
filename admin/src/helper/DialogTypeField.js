import React from 'react';
import CallIcon from '@material-ui/icons/Call';
import MessageIcon from '@material-ui/icons/Message';

const DialogTypeField = props => {
    if (props.record[props.source] === 'chat') {
        return <MessageIcon/>
    }
    if (props.record[props.source] === 'call') {
        return <CallIcon/>
    }

    return null;
}

export default DialogTypeField;