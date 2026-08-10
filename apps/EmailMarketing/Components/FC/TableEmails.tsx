import React, { Component } from 'react'
import FormCampaign, { FormCampaignProps } from './FormCampaign';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormEmail, { FormEmailProps } from './FormEmail';

interface TableEmailsProps extends TableProps {
  idCampaign?: number,
}

const TableEmails = (props: TableEmailsProps) => {
  return <Table
    componentName='TableEmails'
    model='Hubleto/App/Community/EmailMarketing/Models/Email'
    formUrlSlug='email-marketing/emails'
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
      return <FormEmail {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableEmails;