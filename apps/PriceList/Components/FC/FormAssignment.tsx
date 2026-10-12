import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { type FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form from '@hubleto/react-ui/components/fc/Form';

const componentName = 'FormAssignment'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Custom/PriceList';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** FormAssignment */
const FormAssignment = (props: FormProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Assignment'}
    urlSlug='price-lists/assignments'
    endpointParams={{}}
    title={{field: 'id', sub: T.translate('Assignments')}}
    {...props}
  ></Form>;
}

export default FormAssignment;
