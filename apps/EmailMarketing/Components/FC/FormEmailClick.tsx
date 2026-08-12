import React from 'react';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form from '@hubleto/react-ui/components/fc/Form';
import Translator from '@hubleto/react-ui/core/Translator';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormEmailClickProps extends FormProps {}

const componentName = 'FormEmailClick'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/EmailMarketing';

const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormEmailClickProps) => {
  return <>
    <Input field='id_email' />
    <Input field='id_recipient' />
    <Input field='url' />
    <Input field='datetime_clicked' />
    <Input field='log' />
    <Input field='bot_score' />
  </>;
}

/** FormEmailClick */
const FormEmailClick = (props: FormEmailClickProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Click'}
    urlSlug={'email-marketing/emails/clicks'}
    endpointParams={{}}
    onAfterFormInitialized={(form: any) => {}}
    title={{field: 'email', sub: <>{T.translate('Click')}</>}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormEmailClick;

