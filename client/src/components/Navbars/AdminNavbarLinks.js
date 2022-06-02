import React from "react";
// @material-ui/core components
import {generatePath, NavLink} from "react-router-dom";
import Icon from "@material-ui/core/Icon";
// @material-ui/icons
// core components
import Button from "components/CustomButtons/Button.js";
import routes from "../../routes";

export default function AdminNavbarLinks() {
    return (
        <>
            {routes.map((prop, key) => {
                if (prop.display === false) {
                    return null
                }
                return (
                    <NavLink
                        to={generatePath(prop.path, {
                            ...prop.default
                        })}
                        activeClassName="active"
                        key={key}
                        style={{color: "white"}}
                    >
                        <Button color={"transparent"}
                                justIcon={true}
                                simple={false}
                        >
                            {typeof prop.icon === "string" ? (
                                <Icon>
                                    {prop.icon}
                                </Icon>
                            ) : (
                                <prop.icon/>
                            )}
                        </Button>
                    </NavLink>
                );
            })}
        </>
    );
}
