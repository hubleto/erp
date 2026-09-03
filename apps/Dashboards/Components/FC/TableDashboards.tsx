import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormDashboard, { FormDashboardProps } from './FormDashboard';

interface TableDashboardsProps extends TableProps {}

const componentName = 'TableDashboards'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Dashboards';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableDashboards = (props: TableDashboardsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Dashboard'}
    formUrlSlug='dashboards'
    formModalProps={{type: 'right wide'}}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormDashboard {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableDashboards;
