import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { type FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form from '@hubleto/react-ui/components/fc/Form';

const componentName = 'FormHistory'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/PriceLists';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** FormHistory */
const FormHistory = (props: FormProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/History'}
    urlSlug='price-lists/histories'
    endpointParams={{}}
    title={{field: 'id', sub: T.translate('History')}}
    {...props}
  ></Form>;
}

export default FormHistory;
