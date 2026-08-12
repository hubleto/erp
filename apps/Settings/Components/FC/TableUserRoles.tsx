import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormUserRole, { FormUserRoleProps } from './FormUserRole';

interface TebleUserRolesProps extends TableProps {}

const componentName = 'TebleUserRoles'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Settings';

const TebleUserRoles = (props: TebleUserRolesProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/UserRole'}
    formUrlSlug='parent-app-slug/same-url-slug-as-in-form'
    formModalProps={{type: 'right'}}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormUserRole {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TebleUserRoles;
