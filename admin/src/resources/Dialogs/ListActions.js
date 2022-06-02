import React, { cloneElement } from 'react';
import {
    useListContext,
    TopToolbar,
    CreateButton,
    ExportButton,
    sanitizeListRestProps   } from 'react-admin';
import PhoneCallbackIcon from '@material-ui/icons/PhoneCallback';

const ListActions = (props) => {
    const {
        className,
        filters,
        maxResults,
        hasCreate,
        exporter,
        ...rest
    } = props;
    const {
        total,
    } = useListContext();
    return (
        <TopToolbar className={className} {...sanitizeListRestProps(rest)}>
            {cloneElement(filters, { context: 'button' })}
            {hasCreate && <CreateButton icon={<PhoneCallbackIcon/>} label={'Добавить звонок'}/>}
            {exporter && <ExportButton disabled={total === 0} maxResults={maxResults}/>}
        </TopToolbar>
    );
};

export default ListActions;