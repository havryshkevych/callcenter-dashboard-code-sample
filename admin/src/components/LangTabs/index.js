import React from 'react';
import AppBar from '@material-ui/core/AppBar';
import Tabs from '@material-ui/core/Tabs';
import Tab from '@material-ui/core/Tab';
import Box from '@material-ui/core/Box';
import Paper from '@material-ui/core/Paper';
import Flags from 'country-flag-icons/react/3x2'
import {withStyles} from '@material-ui/core/styles';

const StyledTab = withStyles((theme) => ({
    root: {
        minHeight: '25px',
        maxHeight: '25px',
        textTransform: 'none',
        '&:hover': {
            background: '#177e85',
            opacity: 1,
        },
        '&$selected': {
            background: '#288690',
            fontWeight: theme.typography.fontWeightMedium,
        },
        '&:focus': {
            color: '#40a9ff',
        },
    },
    selected: {},
}))((props) => <Tab {...props}/>);

const StyledAppBar = withStyles(() => ({
    root: {
        height: '25px',
    },
}))((props) => <AppBar {...props}/>);


function TabPanel(props) {
    const {children, value, index, ...other} = props;

    return (
        <div
            role="tabpanel"
            hidden={value !== index}
            id={`${index}`}
            aria-labelledby={`tab-${index}`}
            {...other}
        >
            {value === index && (
                <Box>
                    {children}
                </Box>
            )}
        </div>
    );
}

const LangTabs = ({children, hasCreate, hasShow, hasEdit, hasList, basePath, ...props}) => {
    const [langs, setLangs] = React.useState([]);
    const [record, setRecord] = React.useState({});
    let [tab, setTab] = React.useState('');
    const handleTabChange = (event, newValue) => {
        setTab(newValue);
    };

    React.useEffect(() => {
        setRecord(() => {
            return props.record;
        });

    }, [props.record]);

    React.useEffect(() => {
        if (record.translations !== undefined) {
            setLangs(Object.keys(record.translations).sort());
            setTab('tab_' + Object.keys(record.translations).sort()[0]);
        } else {
            setLangs(['ru', 'uk']);
            setTab('tab_ru');
        }
    }, [record])


    if (tab === '') {
        return null;
    }
    const CustomFlag = ({countryCode, ...props}) => {
        if (countryCode === 'uk') {
            countryCode = 'UA';
        }
        let code = typeof countryCode === 'string' ? countryCode.toUpperCase() : countryCode;
        if (Flags[code] === undefined) {
            return countryCode;
        }
        return React.createElement(Flags[code], {...props});
    };
    return (<Paper style={{width: "100%", boxShadow: "unset"}} {...props}>
        <StyledAppBar position="static">
            <Tabs style={{backgroundColor: "#bbb", height: "25px", minHeight: 'unset'}} value={tab}
                  onChange={handleTabChange} aria-label="Translations" indicatorColor="primary" textColor="primary">
                {langs.map((lang) => <StyledTab key={lang} label={<CustomFlag countryCode={lang} width={32}/>}
                                                value={'tab_' + lang}/>)}
            </Tabs>
        </StyledAppBar>
        {langs.map((lang) =>
            <TabPanel
                key={lang}
                value={tab}
                index={'tab_' + lang}>
                {React.Children.map(children, child => {
                        let new_props = {};
                        if (child.props.source !== undefined) {
                            new_props = {
                                source: `translations.${lang}.${child.props.source}`
                            }
                        }
                        return React.cloneElement(child, new_props)
                    }
                )}
            </TabPanel>)}
    </Paper>)
}

export default LangTabs;