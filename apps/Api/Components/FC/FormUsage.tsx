import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormUsageProps extends FormProps {}

const componentName = 'FormUsage'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Api';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormUsageProps) => {
  return <>
    <Input field='id_key' />
    <Input field='controller' />
    <Input field='used_on' />
    <Input field='ip_address' />
    <Input field='status' />
  </>;
}

/** FormUsage */
const FormUsage = (props: FormUsageProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Usage'}
    urlSlug='api/usages'
    endpointParams={{}}
    title={{field: 'id', sub: T.translate('Usage')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormUsage;
