import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { type FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form from '@hubleto/react-ui/components/fc/Form';

const componentName = 'FormRule'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/PriceLists';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** FormRule */
const FormRule = (props: FormProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Rule'}
    urlSlug='price-lists/rules'
    endpointParams={{}}
    title={{field: 'id', sub: T.translate('Rules')}}
    {...props}
  ></Form>;
}

export default FormRule;
