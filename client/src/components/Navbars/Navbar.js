import React from "react";
import classNames from "classnames";
import PropTypes from "prop-types";
// @material-ui/core components
import {makeStyles} from "@material-ui/core/styles";
import AppBar from "@material-ui/core/AppBar";
import Toolbar from "@material-ui/core/Toolbar";
import IconButton from "@material-ui/core/IconButton";
import Hidden from "@material-ui/core/Hidden";
// @material-ui/icons
import Menu from "@material-ui/icons/Menu";
// core components
import AdminNavbarLinks from "./AdminNavbarLinks.js";
import Button from "components/CustomButtons/Button.js";
import logo from "assets/img/logotype_apteka24.svg";
//hooks
import {useRouteName} from "hooks";

import styles from "assets/jss/material-dashboard-react/components/headerStyle.js";
import {generatePath, Link, useLocation} from "react-router-dom";
const useStyles = makeStyles(styles);

export default function Header(props) {
    const classes = useStyles();
    const routeName = useRouteName();
    const {color} = props;
    const appBarClasses = classNames({
        [" " + classes[color]]: color,
    });
    const brand = (
        <div style={{textAlign: 'center', flex: '1 1 auto'}}>
            <Link to={generatePath('/dashboard')}>
                <img src={logo} alt="logo" className={classes.img}/>
            </Link>
        </div>
    );
    return (
        <AppBar className={classes.appBar + appBarClasses}>

            <Toolbar style={{
                display: 'flex',
                flexDirection: 'row',
                flexWrap: 'wrap',
                justifyContent: 'flex-end',
                alignContent: 'center',
                alignItems: 'flex-end',
            }}>
                <Hidden mdDown>
                    {brand}
                </Hidden>
                <AdminNavbarLinks/>
            </Toolbar>
        </AppBar>
    );
}

Header.propTypes = {
    color: PropTypes.oneOf(["primary", "info", "success", "warning", "danger"]),
    handleDrawerToggle: PropTypes.func,
    routes: PropTypes.arrayOf(PropTypes.object),
};
