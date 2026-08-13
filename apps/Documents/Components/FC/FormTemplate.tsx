import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormTemplateProps extends FormProps {}

const componentName = 'FormTemplate'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Documents';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormTemplateProps) => {
  const form = React.useContext(FormMetaContext);
  return <>
    <div className="grid grid-cols-2 gap-1">
      <div>
        <Input field='name' />
        <Input field='used_for' />
      </div>
      <div>
        <Input field='notes' />
      </div>
    </div>
    <div style={{height: 'calc(100vh - 260px)', overflowY: 'scroll'}}>
      <Input field='content' customInputProps={{wrapperCssClass: 'overflow-y-auto'}} />
    </div>
  </>;
}

/** FormTemplate */
const FormTemplate = (props: FormTemplateProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Template'}
    urlSlug='documents/templates'
    endpointParams={{}}
    // onAfterFormInitialized={(form: any) => {}}
    // renderTitle={(): React.JSX.Element => { return <></>; }
    title={{field: 'name', sub: T.translate('Document template')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormTemplate;
