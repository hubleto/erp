import React, { Component } from 'react'
import FormCampaign, { FormCampaignProps } from './FormCampaign';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';

const componentName = 'TableCampaigns';
const parentApp = 'Hubleto/App/Community/EmailMarketing';

const TableCampaigns = (props: TableProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Campaign'}
    formUrlSlug='email-marketing/campaigns'
    formModalProps={{type: 'right wide'}}
    getRowClassName={(table: TableMeta, rowData: any): string => {
      return rowData.is_closed ? 'bg-slate-300' : table.getDefaultRowClassName(rowData);
    }}
    renderCell={(table: TableMeta, columnName: string, column: any, data: any, options: any) => {
      if (columnName == "virt_tags") {
        return data.TAGS.map((tag, key) => {
          return <div key={key} className="text-nowrap mr-2">
            <i style={{color: tag.TAG?.color}} className="fas fa-tag mr-2"></i>
            {tag.TAG?.name}
          </div>;
        });
      } else return table.renderDefaultCell(columnName, column, data, options);
    }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormCampaign {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableCampaigns;
