import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormAuditLogProps extends FormProps {}

const componentName = 'FormAuditLog'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/AuditLogs';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormAuditLogProps) => {
  return <>
    <Input field='datetime' />
    <Input field='type' />
    <Input field='context' />
    <Input field='model' />
    <Input field='record_id' />
    <Input field='message' />
    <Input field='priority' />
    <Input field='id_user' />
    <Input field='ip' />
  </>;
}

/** FormAuditLog */
const FormAuditLog = (props: FormAuditLogProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/AuditLogs'}
    urlSlug='audit-logs'
    title={{fields: ['model', 'id'], sub: T.translate('Audit Log')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormAuditLog;
