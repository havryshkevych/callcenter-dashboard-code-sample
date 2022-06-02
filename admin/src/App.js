import React from "react";
import {
    fetchHydra as baseFetchHydra,
    HydraAdmin,
    hydraDataProvider as baseHydraDataProvider,
    ResourceGuesser
} from "@api-platform/admin";
import {parseHydraDocumentation} from "@api-platform/api-doc-parser";
import {Redirect, Route} from "react-router-dom";
import authProvider from "./authProvider";
import polyglotI18nProvider from 'ra-i18n-polyglot';
import englishMessages from 'ra-language-english';
import russianMessages from 'ra-language-russian';
import Layout from "./components/Layout";
import resources from "./resources";
import myDataProvider from './dataProvider';

const entrypoint = process.env.REACT_APP_API_ENTRYPOINT;
const token = localStorage.getItem('token');
const fetchHeaders = new Headers({Authorization: 'Basic ' + token,});
const fetchHydra = (url, options = {}) => baseFetchHydra(url, {
    ...options,
    headers: new Headers(fetchHeaders),
});

const apiDocumentationParser = entrypoint => parseHydraDocumentation(entrypoint, {headers: new Headers(fetchHeaders)})
    .then(
        ({api}) => ({api}),
        (result) => {
            switch (result.status) {
                case 401:
                    return Promise.resolve({
                        api: result.api,
                        customRoutes: [
                            <Route path="/" render={() => {
                                return window.localStorage.getItem("token") ? window.location.reload() :
                                    <Redirect to="/login"/>
                            }}/>
                        ],
                    });
                default:
                    return Promise.reject(result);
            }
        },
    );

const dataProvider = baseHydraDataProvider(
    entrypoint,
    fetchHydra,
    apiDocumentationParser,
    true
);

const newDataProvider = myDataProvider(dataProvider);

const i18nMessages = {
    en: englishMessages,
    ru: russianMessages
};

const i18nProvider = polyglotI18nProvider(locale => i18nMessages[locale], 'ru', {
    allowMissing: true,
    onMissingKey: (key, _, __) => key
});

export const LangContext = React.createContext({});


export default () => {
    const [locale, setLocale] = React.useState('ru');
    fetchHeaders.set('Accept-Language', locale);
    document.title = 'Callcenter Admin - ' + process.env.REACT_APP_VERSION;

    return (
        <LangContext.Provider value={{locale, setLocale}}>
            <HydraAdmin
                layout={Layout}
                dataProvider={newDataProvider}
                authProvider={authProvider}
                i18nProvider={i18nProvider}
                entrypoint={process.env.REACT_APP_API_ENTRYPOINT}
            >
                <ResourceGuesser options={{label: "Диалоги", show: true}} name="admin/dialogs" {...resources.dialogs} />
                <ResourceGuesser options={{label: "Операторы", show: true}} name="admin/users" {...resources.users} />
                <ResourceGuesser options={{label: "Оценивание", show: false}} name="admin/scorings" {...resources.scorings} />
                <ResourceGuesser options={{label: "Рабочие часы", show: true}} name="admin/active-times" {...resources.activeTime} />
                <ResourceGuesser options={{label: "Контроль знаний", show: true}} name="admin/knowledge-scorings" {...resources.knowledgeScorings} />
                <ResourceGuesser options={{label: "Оценки", show: false}}
                                 name="admin/evaluations" {...resources.evaluations} />
                <ResourceGuesser options={{label: "Критерии оценивания", show: true}}
                                 name="admin/criterias" {...resources.criterias} />
                <ResourceGuesser options={{label: "Запись диалога", show: false}}
                                 name="admin/dialog-records" {...resources.dialogRecords} />
                <ResourceGuesser options={{label: "Зоны", show: true}}
                                 name="admin/zones" {...resources.zones} />
            </HydraAdmin>
        </LangContext.Provider>
    )
};
