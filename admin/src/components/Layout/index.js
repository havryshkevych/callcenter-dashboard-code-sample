import * as React from 'react';
import Menu from '../Menu';
import {AppBar, Layout} from 'react-admin';
import {Box, Typography} from '@material-ui/core';

const MyAppBar = props => {
    return (
        <AppBar {...props}>
            <Box flex="1">
                <Typography variant="h6" id="react-admin-title"/>
            </Box>
        </AppBar>
    );
}


export default ({locale, setLocale, ...props}) => <Layout {...props} menu={Menu} appBar={MyAppBar}/>;
