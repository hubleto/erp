import React, { useState, createRef } from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import VarcharInput from '@hubleto/react-ui/components/fc/Inputs/Varchar';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import request from '@hubleto/react-ui/core/Request';
import TableUsages from './TableUsages';
import TablePermissions from './TablePermissions';

export interface FormKeyProps extends FormProps {}

const componentName = 'FormKey'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Api';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormKeyProps) => {
  return <>
    <Input field='key' />
    <Input field='valid_until' />
    <Input field='is_enabled' customInputProps={{yesText: 'Enabled'}} />
    <Input field='notes' />
    <Input field='ip_address_blacklist' />
    <Input field='ip_address_whitelist' />
  </>;
}

/** TabTest */
const TabTest = (props: FormKeyProps) => {
  const form = React.useContext(FormMetaContext);
  const key: string = useRecordField('key', '');

  const [testRequest, setTestRequest] = useState(null);
  const [testResponse, setTestResponse] = useState(null);

  const refInputApp = createRef();
  const refInputController = createRef();
  const refInputVars = createRef();

  return form.id > 0 ? <>
    <table className='table-default dense'>
      <thead>
        <tr><th>{T.translate('Parameter')}</th><th>{T.translate('Value')}</th></tr>
      </thead>
      <tbody>
        <tr>
          <td>{T.translate('Endpoint')}</td>
          <td className='m-2'>{globalThis.hubleto.config.projectUrl + '/api/call'}</td>
        </tr>
        <tr>
          <td>{T.translate('Key')}</td>
          <td className='m-2'>{key}</td>
        </tr>
        <tr>
          <td>{T.translate('App')}</td>
          <td className='m-2'><VarcharInput uid='app' value='Hubleto\App\Community\Contacts' ref={refInputApp}></VarcharInput></td>
        </tr>
        <tr>
          <td>{T.translate('Controller')}</td>
          <td className='m-2'><VarcharInput uid='controller' value='GetContacts' ref={refInputController}></VarcharInput></td>
        </tr>
        <tr>
          <td>{T.translate('Vars')}</td>
          <td className='m-2'><VarcharInput uid='vars' value='{"idCustomer":0}' ref={refInputVars}></VarcharInput></td>
        </tr>
      </tbody>
    </table>
    <button
      className='btn btn-primary-outline mt-2'
      onClick={() => {
        let testRequest = {
          key: key,
          app: refInputApp.current.state.value,
          controller: refInputController.current.state.value,
          vars: refInputVars.current.state.value,
        }

        setTestRequest(testRequest);
        request.post(
          'api/call',
          testRequest,
          {},
          (data: any) => { setTestResponse(data); },
          (error: any) => { setTestResponse(error); }
        )
      }}
    >
      <span className='icon'><i className='fas fa-bolt'></i></span>
      <span className='text'>{T.translate('Run test')}</span>
    </button>
    {testResponse ?
      <div className='card mt-2'>
        <div className='card-header'>{T.translate('Test result')}</div>
        <div className='card-body'>
          <pre className='text-xs bg-yellow-50 p-2'>
            POST accounts/wai-blue/api/call{"\n"}
            {"  "}-H 'Content-type: application/json'{"\n"}
            {"  "}-D '&#123;{"\n"}
            {"    "}key: {testRequest.key}{"\n"}
            {"    "}app: {testRequest.app}{"\n"}
            {"    "}controller: {testRequest.controller}{"\n"}
            {"    "}vars: {JSON.stringify(testRequest.vars)}{"\n"}
            {"  "}&#125;'{"\n"}
          </pre>
          <pre className='text-xs bg-blue-50 p-2'>{JSON.stringify(testResponse, null, 2)}</pre>
        </div>
      </div>
    : null}
  </> : null;
}

/** TabPermissions */
const TabPermissions = (props: FormKeyProps) => {
  const form = React.useContext(FormMetaContext);
  return form.id > 0 ?
    <TablePermissions
      uid={props.uid + "_table_permissions"}
      tag={props.uid + "_table_permissions"}
      parentForm={form}
      idKey={form.id}
    />
  : null;
}

/** TabUsage */
const TabUsage = (props: FormKeyProps) => {
  const form = React.useContext(FormMetaContext);
  return form.id > 0 ?
    <TableUsages
      uid={props.uid + "_table_usage"}
      tag={props.uid + "_table_usage"}
      parentForm={form}
      idKey={form.id}
      readonly={true}
    />
  : null;
}

/** FormKey */
const FormKey = (props: FormKeyProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Key'}
    urlSlug='api/keys'
    endpointParams={{}}
    title={{field: 'key', sub: T.translate('API Key')}}
    tabs={{
      default: {title: <b>{T.translate('API Key')}</b>, content: () => <TabDefault {...props} />},
      permissions: {title: T.translate('Permissions'), content: () => <TabPermissions {...props} />},
      test: {title: T.translate('Test'), content: () => <TabTest {...props} />},
      usage: {title: T.translate('Usage'), content: () => <TabUsage {...props} />},
    }}
    {...props}
  ></Form>;
}

export default FormKey;
