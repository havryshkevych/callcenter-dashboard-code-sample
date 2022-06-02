import isPlainObject from 'lodash.isplainobject';

export default dataProvider => {

    return {
        ...dataProvider,
        create: (resource, params) => {
            if (resource === 'dialog/import-call') {
                const containFile = (element) =>
                    isPlainObject(element) &&
                    Object.values(element).some((value) => value instanceof File);
                const body = new FormData();
                Object.entries(params.data).map(([key, value]) => {
                    if (key === 'records') {
                        return value.forEach(record => {
                            body.append(
                                key+'[]',
                                Object.values(record).find((value) => value instanceof File),
                            )
                        });
                    }
                    if (containFile(value)) {
                        return body.append(
                            key,
                            Object.values(value).find((value) => value instanceof File),
                        );
                    }
                    if ('function' === typeof value.toJSON) {
                        return body.append(key, value.toJSON());
                    }
                    if (isPlainObject(value) || Array.isArray(value)) {
                        return body.append(key, JSON.stringify(value));
                    }
                    return body.append(key, value);
                });

                return dataProvider.create(resource, {data: body});
            }
            if (resource === 'admin/users' && params.data.photo) {
                const newPictures = params.data.photo;

                return convertFileToBase64(newPictures)
                    .then(transformedNewPictures =>
                        dataProvider.create(resource, {
                            ...params,
                            data: {
                                ...params.data,
                                photo: transformedNewPictures,
                            },
                        })
                    );
            }

            return dataProvider.create(resource, params);
        },
        update: (resource, params) => {
            if (resource === 'admin/users' && params.data.photo) {
                if (params.data.photo.rawFile instanceof File) {
                    const newPictures = params.data.photo;

                    return convertFileToBase64(newPictures)
                        .then(transformedNewPictures =>
                            dataProvider.update(resource, {
                                ...params,
                                data: {
                                    ...params.data,
                                    photo: transformedNewPictures,
                                },
                            })
                       );
                }
            }

            return dataProvider.update(resource, params);
        },
    }
}

const convertFileToBase64 = file =>
    new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result);
        reader.onerror = reject;
        reader.readAsDataURL(file.rawFile);
    });