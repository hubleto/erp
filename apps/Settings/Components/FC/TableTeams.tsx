import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormTeam from './FormTeam';

interface TableTeamsProps extends TableProps {}

const componentName = 'TableTeams'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Settings';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableTeams = (props: TableTeamsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Team'}
    formUrlSlug='parent-app-slug/same-url-slug-as-in-form'
    formModalProps={{type: 'centered'}}
    renderCell={(table: TableMeta, columnName: string, column: any, data: any, options: any) => {
      if (columnName == "members") {
        return data.MEMBERS.map((member, key) => {
          return <div className='badge' key={data.id + '-members-' + key}>{member.MEMBER.email}</div>;
        });
      } else {
        return table.renderDefaultCell(columnName, column, data, options);
      }
    }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormTeam {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableTeams;
