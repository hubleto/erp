import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

const componentName = 'FormTemplate'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/AppXXX';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** FormTemplate */
const FormTemplate = (props: FormProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Template'}
    urlSlug='mail/templates'
    title={{field: 'subject', sub: T.translate('Template')}}
    tabs={{default: {content: () => <>
      <Input field='subject' />
      <Input field='body_text' />
      <Input field='body_html' />
    </>}}}
    {...props}
  ></Form>;
}

export default FormTemplate;