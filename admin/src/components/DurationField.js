import React from 'react';
import {useRecordContext} from 'react-admin';
import moment from 'moment';

const DurationField = (props) => {
    const { source } = props;
    const record = useRecordContext(props);
    let duration = moment.duration(record[source], 'seconds');
    return <span>{moment.utc(duration.as('milliseconds')).format('HH:mm:ss')}</span>;
}

export default DurationField;