import * as React from 'react';
import {useSelector} from 'react-redux';
import {useMediaQuery} from '@material-ui/core';
import {getResources, MenuItemLink} from 'react-admin';
import Storage from '@material-ui/icons/Storage';
import EditAttributesIcon from '@material-ui/icons/EditAttributes';
import Collapse from '@material-ui/core/Collapse';
import ExpandLess from '@material-ui/icons/ExpandLess';
import ExpandMore from '@material-ui/icons/ExpandMore';
import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemIcon from '@material-ui/core/ListItemIcon';
import ListItemText from '@material-ui/core/ListItemText';

const Menu = ({onMenuClick, logout}) => {
    const isXSmall = useMediaQuery(theme => theme.breakpoints.down('xs'));
    const open = useSelector(state => state.admin.ui.sidebarOpen);
    const [openMenu, setOpen] = React.useState({});
    const handleClick = (param) => {
        let newState = {...openMenu};
        newState[param] = !!!openMenu[param];
        setOpen(newState);
    };
    const groupBy = function (xs, key) {
        return xs.reduce(function (rv, x) {
            (rv[key.split('.').reduce((o, i) => o[i], x)] = rv[key.split('.').reduce((o, i) => o[i], x)] || []).push(x);
            return rv;
        }, {});
    };
    const resources = useSelector(getResources);
    const menu = groupBy(resources, "options.submenu");

    return (<div>
            {Object.keys(menu).map(resourceKey => {
                return menu[resourceKey].map(resource => {
                    if (!resource.options.show || !!resource.options.submenu) {
                        return null;
                    }
                    return (<MenuItemLink
                        key={resource.name}
                        to={`/${resource.name}`}
                        primaryText={resource.options.label}
                        leftIcon={<Storage/>}
                        onClick={onMenuClick}
                        sidebarIsOpen={open}
                    />);
                })
            })}

            {Object.keys(menu).map(resourceKey => {
                if (resourceKey === 'undefined' || !menu[resourceKey].find(o => o.options.show === true)) {
                    return null;
                }
                return (<div key={'collapse' + resourceKey}>
                    <ListItem button onClick={() => {
                        handleClick(resourceKey)
                    }}>
                        <ListItemIcon>
                            <EditAttributesIcon/>
                        </ListItemIcon>
                        <ListItemText primary={resourceKey}/>
                        {openMenu[resourceKey] ? <ExpandLess/> : <ExpandMore/>}
                    </ListItem>
                    <Collapse in={openMenu[resourceKey]} timeout="auto" unmountOnExit>
                        <List component="div" disablePadding>
                            {menu[resourceKey].map(resource => {
                                if (!resource.options.show || resource.options.submenu !== resourceKey) {
                                    return null;
                                }
                                return (<MenuItemLink
                                    key={resourceKey + resource.name}
                                    to={`/${resource.name}`}
                                    primaryText={resource.options.label}
                                    leftIcon={<Storage/>}
                                    onClick={onMenuClick}
                                    sidebarIsOpen={open}
                                />);
                            })}
                        </List>
                    </Collapse>
                </div>);
            })}

            {isXSmall && logout}
        </div>
    );
}

export default Menu;
