import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormAutomatProps extends FormProps {}

const componentName = 'FormAutomat'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Workflow';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormAutomatProps) => {
  return <div className="flex flex-col gap-2" >
    <Input field='name' />
    <Input field='execution_order' />
    <Input field='description' />
    <Input field='conditions' />
    <Input field='actions' />
  </div>;
}

/** FormAutomat */
const FormAutomat = (props: FormAutomatProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Automat'}
    urlSlug='workflow/automats'
    endpointParams={{}}
    title={{field: 'name', sub: T.translate('Workflow automat')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormAutomat;
