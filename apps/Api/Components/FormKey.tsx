import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Varchar from '@hubleto/react-ui/core/Inputs/Varchar';
import TablePermissions from './TablePermissions';
import TableUsages from './TableUsages';
import request from '@hubleto/react-ui/core/Request';

interface FormKeyProps extends HubletoFormProps { }
interface FormKeyState extends HubletoFormState {
  testResult: any,
}

export default class FormKey<P, S> extends HubletoForm<FormKeyProps, FormKeyState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Api/Models/Key',
  }

  props: FormKeyProps;
  state: FormKeyState;

  refInputApp: any = null;
  refInputController: any = null;
  refInputVars: any = null;

  translationContext: string = 'Hubleto\\App\\Community\\Api\\Loader';
  translationContextInner: string = 'Components\\FormKey';

  constructor(props: FormKeyProps) {
    super(props);
    this.state = this.getStateFromProps(props);
    this.refInputApp = React.createRef();
    this.refInputController = React.createRef();
    this.refInputVars = React.createRef();
  }

  getStateFromProps(props: FormKeyProps) {
    return {
      ...super.getStateFromProps(props),
      testResult: null,
      tabs: [
        { uid: 'default', title: this.translate('Key') },
        { uid: 'permissions', title: this.translate('Permissions') },
        { uid: 'test', title: this.translate('Test') },
        { uid: 'usage', title: this.translate('Usage') },
      ]
    };
  }

  getRecordFormUrl(): string {
    return 'api/keys/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    return <>
      <small>API Key</small>
      <h2>{this.state.record.key ?? '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          {this.inputWrapper('key')}
          {this.inputWrapper('valid_until')}
          {this.inputWrapper('is_enabled')}
          {this.inputWrapper('notes')}
          {this.inputWrapper('ip_address_blacklist')}
          {this.inputWrapper('ip_address_whitelist')}
          {this.inputWrapper('created')}
          {this.inputWrapper('id_created_by')}
        </>;
        break;
      case 'permissions':
        return R.id > 0 ?
          <TablePermissions
            uid={this.props.uid + "_table_permissions"}
            tag={this.props.uid + "_table_permissions"}
            parentForm={this}
            idKey={R.id}
          />
        : null;
      break;
      case 'test':
        console.log(this.refInputApp.current, this.refInputController.current, this.refInputVars.current);
        return R.id > 0 ? <>
          <table className='table-default dense'>
            <thead>
              <tr><th>Parameter</th><th>Value</th></tr>
            </thead>
            <tbody>
              <tr>
                <td>Endpoint</td>
                <td className='m-2'>{globalThis.main.config.projectUrl + '/api/call'}</td>
              </tr>
              <tr>
                <td>Key</td>
                <td className='m-2'>{R.key}</td>
              </tr>
              <tr>
                <td>App</td>
                <td className='m-2'><Varchar uid='app' value='Hubleto\App\Community\Contacts' ref={this.refInputApp}></Varchar></td>
              </tr>
              <tr>
                <td>Controller</td>
                <td className='m-2'><Varchar uid='controller' value='GetContacts' ref={this.refInputController}></Varchar></td>
              </tr>
              <tr>
                <td>Vars</td>
                <td className='m-2'><Varchar uid='vars' value='{"idCustomer":0}' ref={this.refInputVars}></Varchar></td>
              </tr>
            </tbody>
          </table>
          <button
            className='btn btn-primary-outline mt-2'
            onClick={() => {
              request.post(
                'api/call',
                {
                  key: R.key,
                  app: this.refInputApp.current.state.value,
                  controller: this.refInputController.current.state.value,
                  vars: this.refInputVars.current.state.value,
                },
                {},
                (data: any) => {
                  this.setState({testResult: data});
                }
              )
            }}
          >
            <span className='icon'><i className='fas fa-bolt'></i></span>
            <span className='text'>{this.translate('Run test')}</span>
          </button>
          {this.state.testResult ?
            <div className='card mt-2'>
              <div className='card-header'>{this.translate('Test result')}</div>
              <div className='card-body'>
                <pre className='text-xs bg-yellow-50 p-2'>
                  POST accounts/wai-blue/api/call{"\n"}
                  {"  "}-H 'Content-type: application/json'{"\n"}
                  {"  "}-D '&#123;{"\n"}
                  {"    "}key: {R.key}{"\n"}
                  {"    "}app: {this.refInputApp.current.state.value}{"\n"}
                  {"    "}controller: {this.refInputController.current.state.value}{"\n"}
                  {"    "}vars: {JSON.stringify(this.refInputVars.current.state.value)}{"\n"}
                  {"  "}&#125;'{"\n"}
                </pre>
                <pre className='text-xs bg-blue-50 p-2'>{JSON.stringify(this.state.testResult, null, 2)}</pre>
              </div>
            </div>
          : null}
        </> : null;
      break;
      case 'usage':
        return R.id > 0 ?
          <TableUsages
            uid={this.props.uid + "_table_usage"}
            tag={this.props.uid + "_table_usage"}
            parentForm={this}
            idKey={R.id}
            readonly={true}
          />
        : null;
      break;
    }
  }

}
