import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormUser, { FormUserProps } from './FormUser';

interface TableUsersProps extends TableProps {}

const componentName = 'TableUsers'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Settings';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableUsers = (props: TableUsersProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={'Hubleto/App/Community/Auth/Models/User'}
    formUrlSlug='parent-app-slug/same-url-slug-as-in-form'
    formModalProps={{type: 'centered'}}
    renderCell={(table: TableMeta, columnName: string, column: any, data: any, options: any) => {
      if (columnName == "roles") {
        return data.ROLES.map((role, key) => {
          return <div className='badge' key={data.id + '-roles-' + key}>{role.role}</div>;
        });
      } else if (columnName == "teams") {
        return data.TEAMS.map((team, key) => {
          return <div
            className='badge' key={data.id + '-roles-' + key}
            style={{borderLeft: '1em solid ' + team.color}}
          >{team.name}</div>;
        });
      } else {
        return table.renderDefaultCell(columnName, column, data, options);
      }
    }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormUser {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableUsers;
