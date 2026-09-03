import React, { Component } from 'react'
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Table from '@hubleto/react-ui/components/fc/Table';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Translator from '@hubleto/react-ui/core/Translator';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

const componentName = 'FormTeam';
const parentApp = 'Hubleto/App/Community/Settings';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormProps) => {
  const form = React.useContext(FormMetaContext);

  return <div className='w-full flex gap-2'>
    <div className="p-4 flex-1 text-center">
      <i className="fas fa-users text-primary" style={{fontSize: '8em'}}></i>
    </div>
    <div className="flex-6">
      <Input field='name' />
      <Input field='description' />
      <Input field='id_manager' />
      {props.id < 0 ?
        <div className="badge badge-info">{T.translate('First create team, then you will be prompted to add members.')}</div>
      :
        <Table
          uid='teams_members'
          model='Hubleto/App/Community/Settings/Models/TeamMember'
          parentForm={form}
          endpointParams={{idTeam: props.id}}
          itemsPerPage={35}
        ></Table>
      }
    </div>
  </div>;
}

const FormTeam = (props: FormProps) => {

  return <Form
    componentName={componentName}
    model={parentApp + '/Models/Team'}
    urlSlug='settings/teams'
    title={{field: 'name', sub: T.translate('Team')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
};

export default FormTeam;