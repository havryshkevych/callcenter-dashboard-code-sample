import React from "react";
// @material-ui/core components
import {makeStyles} from "@material-ui/core/styles";
import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemIcon from '@material-ui/core/ListItemIcon';
import ListItemText from '@material-ui/core/ListItemText';
import {generatePath, Link, Route, useLocation, useParams} from 'react-router-dom';
import DataUsageIcon from '@material-ui/icons/DataUsage';
import AccessTimeIcon from '@material-ui/icons/AccessTime';
import HighQualityIcon from '@material-ui/icons/HighQuality';
import LocalLibraryIcon from '@material-ui/icons/LocalLibrary';
import QuestionAnswerIcon from '@material-ui/icons/QuestionAnswer';
import NoteIcon from '@material-ui/icons/Note';
// core components
import GridItem from "components/Grid/GridItem.js";
import GridContainer from "components/Grid/GridContainer.js";
import Card from "components/Card/Card.js";
import CardHeader from "components/Card/CardHeader.js";
import CardAvatar from "components/Card/CardAvatar.js";
import CardBody from "components/Card/CardBody.js";
import routes from "routes.js";
import Gravatar from 'react-gravatar';
import dataProvider from "../../dataProvider";

const styles = {
    cardCategoryWhite: {
        color: "rgba(255,255,255,.62)",
        margin: "0",
        fontSize: "14px",
        marginTop: "0",
        marginBottom: "0",
    },
    cardTitleWhite: {
        color: "#FFFFFF",
        marginTop: "0px",
        minHeight: "auto",
        fontWeight: "300",
        fontFamily: "'Roboto', 'Helvetica', 'Arial', sans-serif",
        marginBottom: "3px",
        textDecoration: "none",
    },
};

const useStyles = makeStyles(styles);

export default function UserProfile({match}) {
    const classes = useStyles();
    const routeData = routes.find(({path}) => path === match.path)
    let { userId, tabName } = useParams();
    const tabData = tabName === undefined ? routeData.tabs[0] : routeData.tabs.find(({path}) => path === tabName);
    const [user, setUser] = React.useState({
        email: '',
        name: '---',
        description: '---'
    });

    React.useEffect(async () => {
        let active = true;
        await dataProvider().get('/users/' + userId).then(res => {
            if (active) {
                setUser(res.data);
            }
        });
        return () => {
            active = false;
        }
    },[userId]);

    return (
        <div>
            <GridContainer>
                <GridItem xs={12} sm={12} md={4}>
                    <Card profile>
                        <CardAvatar profile>
                            <a href="#" onClick={(e) => e.preventDefault()}>
                                <Gravatar rating={'x'} src={user.photo} email={user.email} default={'retro'} size={400}/>
                            </a>
                        </CardAvatar>
                        <CardBody profile>
                            <h6 className={classes.cardCategory}>{user.description}</h6>
                            <h4 className={classes.cardTitle}>{user.name}</h4>
                            <List component="nav" aria-label="main mailbox folders">
                                {routeData.tabs.map((tab, index) => {
                                    const {path, icon, name} = tab;

                                    if (tab.role && !user?.roles ) {
                                        return null;
                                    }
                                    if (tab.role) {
                                        if (!user?.roles.includes(tab.role)) {
                                            return null;
                                        }
                                    }
                                    return (
                                        <ListItem button key={index} component={Link} to={generatePath(match.path, {
                                            tabName: path,
                                            userId: userId
                                        })} disabled={tab?.disabled}>
                                            <ListItemIcon>
                                                {icon}
                                            </ListItemIcon>
                                            <ListItemText primary={name}/>
                                        </ListItem>
                                    );
                                })}
                            </List>
                        </CardBody>
                    </Card>

                    {user.currentRank && <Card>
                        <CardHeader color={"primary"} style={{boxShadow:`0 4px 20px 0 ${user.currentRank.zone.color}, 0 7px 10px -5px ${user.currentRank.zone.color}`}}>
                            <h4 className={classes.cardTitleWhite}>Рекомендации по итогам</h4>
                            <p className={classes.cardCategoryWhite}>Предыдущего месяца</p>
                        </CardHeader>
                        <CardBody dangerouslySetInnerHTML={{__html: user.currentRank.zone.hint}}>

                        </CardBody>
                    </Card>}
                </GridItem>
                <GridItem xs={12} sm={12} md={8}>
                    <Route path={generatePath(match.path, {
                        tabName: tabName,
                        userId: userId,
                    })} component={() => <tabData.component user={user}/>}/>
                </GridItem>
            </GridContainer>
        </div>
    );
}
