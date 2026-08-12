import React, { useState, useEffect } from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormMeta, FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import request from '@hubleto/react-ui/core/Request';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import Divider from '@hubleto/react-ui/components/fc/FormComponents/Divider';
import Table from '@hubleto/react-ui/components/fc/Table';

export interface FormUserProps extends FormProps {}

const componentName = 'FormUser'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Settings';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormUserProps) => {

  const [appsInfo, setAppsInfo] = useState(null);

  useEffect(() => {
    request.get(
      'api/get-apps-info',
      {},
      (appsInfo: any) => { setAppsInfo(appsInfo); }
    );
  }, [])

  const form = React.useContext(FormMetaContext);
  const appsRaw: any = useRecordField('apps');
  const permissionsRaw: any = useRecordField('permissions');

  let permissions: any = {};
  let apps: any = [];

  try {
    permissions = JSON.parse(permissionsRaw);
  } catch (ex) {
    permissions = {};
  }

  if (!permissions) permissions = {};


  try {
    apps = JSON.parse(appsRaw);
  } catch (ex) {
    apps = [];
  }

  if (!apps) apps = [];

  return <>
    <div className='w-full flex flex-col md:flex-row gap-2'>
      {props.id == -1 && !globalThis.hubleto.isPremium ?
        <div className="badge badge-warning text-lg w-full block p-8">
          {T.translate('You may add new users only in Premium account.')}<br/>
          <br/>
          <a href={globalThis.hubleto.config.projectUrl + '/cloud'} className="btn btn-primary">
            <span className="icon"><i className="fas fa-medal"></i></span>
            <span className="text">{T.translate('Activate Premium account')}</span>
          </a>
        </div>
      : <>
        <div className="p-4 flex-1 text-center">
          <i className="fas fa-user text-primary" style={{fontSize: '8em'}}></i>
          <div className='mt-2'>
            <Input field='photo' renderOnlyInputField />
          </div>
        </div>
        <div className="flex-6">
          <div className='flex gap-2 flex-col md:flex-row'>
            <div className="flex-1">
              <Divider>{T.translate('About the user')}</Divider>
              <Input field='type' />
              <Input field='first_name' />
              <Input field='last_name' />
              <Input field='nick' />
              <Input field='position' />
              <Input field='email' />
              <Input field='phone_1' />
              <Input field='phone_2' />
              <Input field='language' />
              <Input field='timezone' />
              <Input field='id_default_company' />

              <Divider>{T.translate('Access to Hubleto')}</Divider>
              <Input field='is_active' customInputProps={{
                readonly: props.id == globalThis.hubleto.idUser,
              }} />
              <Input field='password' />

              <Divider>{T.translate('Roles')}</Divider>

              {props.id < 0 ?
                <div className="badge badge-info">{T.translate('First create user, then you will be prompted to assign roles.')}</div>
              :
                <Table
                  uid='user_roles'
                  model='Hubleto/App/Community/Auth/Models/UserHasRole'
                  parentForm={form}
                  endpointParams={{idUser: props.id}}
                ></Table>
              }
            </div>
            <div className="flex-1 md:flex-row">
              <Divider>{T.translate('Permissions for records with designated owner or manager')}</Divider>
              <div className='list'>
                <div className='list-item'><div className='flex gap-2 justify-between p-1'>
                  <div>{T.translate('Reading')}</div>
                  <div>
                    <select
                      onChange={(event) => {
                        permissions.recordsRead = event.currentTarget.value;
                        form.changeRecord({permissions: JSON.stringify(permissions)});
                      }}
                      value={permissions.recordsRead ?? 'owned'}
                    >
                      <option value='owned'>{T.translate('Can read only owned records')}</option>
                      <option value='owned-and-managed'>{T.translate('Can read only owned and managed records')}</option>
                      <option value='all'>{T.translate('Can read all records')}</option>
                    </select>
                  </div>
                </div></div>
                <div className='list-item'><div className='flex gap-2 justify-between p-1'>
                  <div>{T.translate('Modifying')}</div>
                  <div>
                    <select
                      onChange={(event) => {
                        permissions.recordsModify = event.currentTarget.value;
                        form.changeRecord({permissions: JSON.stringify(permissions)});
                      }}
                      value={permissions.recordsModify ?? 'owned'}
                    >
                      <option value='owned'>{T.translate('Can modify only owned records')}</option>
                      <option value='owned-and-managed'>{T.translate('Can modify only owned and managed records')}</option>
                      <option value='all'>{T.translate('Can modify all records')}</option>
                    </select>
                  </div>
                </div></div>
              </div>
              <Divider>{T.translate('Access to apps')}</Divider>
              <div className="list">
                {appsInfo ? <>
                  {Object.keys(appsInfo).map((appNamespace: any) => {
                    const app = appsInfo[appNamespace];
                    const permitted = app.permittedForAllUsers || apps.includes(appNamespace);
                    return <button
                      key={appNamespace}
                      className={
                        "btn btn-small btn-list-item "
                        + (app.permittedForAllUsers ? "btn-disabled" : (permitted ? "btn-primary-outline" : "btn-transparent"))
                      }
                      onClick={() => {
                        if (!app.permittedForAllUsers) {
                          if (apps.includes(appNamespace)) {
                            let appsNew = [];
                            for (let i in apps) {
                              if (apps[i] != appNamespace) appsNew.push(apps[i]);
                            }
                            apps = appsNew;
                          } else {
                            apps.push(appNamespace);
                          }

                          form.changeRecord({apps: JSON.stringify(apps)});
                        }
                      }}
                    >
                      <span className="icon"><i className={"fas fa-" + (permitted ? "square-check" : "square")}></i></span>
                      <span className="text">
                        {app.manifest.name}
                      </span>
                    </button>
                  })}
                </> : null}
              </div>
            </div>
          </div>
        </div>
      </>}
    </div>
  </>;
}

/** FormUser */
const FormUser = (props: FormUserProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={'Hubleto/App/Community/Auth/Models/User'}
    urlSlug='parent-app-slug/same-url-slug-as-in-table'
    endpointParams={{}}
    title={{field: 'some-field-of-the-record', sub: T.translate(componentName)}}
    renderTitle={(form: FormMeta): React.JSX.Element => {
      const firstName = useRecordField('first_name', '');
      const lastName = useRecordField('last_name', '');
      const title = (firstName + ' ' + lastName).trim();

      return <>
        <small>{T.translate('User')}</small>
        <h2>{title == '' ? '-' : title}</h2>
      </>;
    }}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormUser;

